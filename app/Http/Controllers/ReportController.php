<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Technician;
use App\Models\Outlet;
use App\Models\Customer;
use Illuminate\Http\Request;

use App\Models\Review;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from    = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to      = $request->date_to   ?? now()->toDateString();
        $outletId = $request->outlet_id;

        $outlets = Outlet::where('status','active')->get();

        // Helper closures to handle completion and payment date ranges safely
        $completedDateFilter = function($query, $dateFrom, $dateTo) {
            return $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('completed_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                  ->orWhere(function($sub) use ($dateFrom, $dateTo) {
                      $sub->whereNull('completed_at')
                          ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
                  });
            });
        };

        $paymentDateFilter = function($query, $dateFrom, $dateTo) {
            return $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('paid_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                  ->orWhere(function($sub) use ($dateFrom, $dateTo) {
                      $sub->whereNull('paid_at')
                          ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
                  });
            });
        };

        // Determine previous period dates for comparison
        $startDate = \Carbon\Carbon::parse($from);
        $endDate = \Carbon\Carbon::parse($to);
        $days = $startDate->diffInDays($endDate) + 1;
        $prevFrom = $startDate->copy()->subDays($days)->toDateString();
        $prevTo = $startDate->copy()->subDay()->toDateString();

        // Helper to fetch statistics for a period
        $getStats = function($dateFrom, $dateTo) use ($request, $completedDateFilter, $paymentDateFilter, $outletId) {
            // 1. Revenue
            $revenueQuery = Payment::where('status', 'paid');
            $paymentDateFilter($revenueQuery, $dateFrom, $dateTo);
            if ($outletId) {
                $revenueQuery->whereHas('booking', function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                });
            }
            $revenue = $revenueQuery->sum('amount');
            
            // 2. Orders Served
            $ordersQuery = Booking::where('status', 'completed');
            $completedDateFilter($ordersQuery, $dateFrom, $dateTo);
            if ($outletId) {
                $ordersQuery->where('outlet_id', $outletId);
            }
            $orders = $ordersQuery->count();
            
            // 3. Avg Per Order
            $avgQuery = Booking::where('status', 'completed');
            $completedDateFilter($avgQuery, $dateFrom, $dateTo);
            if ($outletId) {
                $avgQuery->where('outlet_id', $outletId);
            }
            $avg = $avgQuery->avg('total_amount') ?? 0;
            
            // 4. Satisfaction (Review Rating * 20)
            $reviewsQuery = Review::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            if ($outletId) {
                $reviewsQuery->whereHas('booking', function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                });
            }
            $avgRating = $reviewsQuery->avg('rating');
            $satisfaction = $avgRating ? $avgRating * 20 : 100.0;
            $reviewsCount = $reviewsQuery->count();
            
            return [
                'revenue' => (float)$revenue,
                'orders' => (int)$orders,
                'avg' => (float)$avg,
                'satisfaction' => (float)$satisfaction,
                'reviews_count' => (int)$reviewsCount,
            ];
        };

        $current = $getStats($from, $to);
        $previous = $getStats($prevFrom, $prevTo);

        // Helper to format percentage change
        $getPercentChange = function($curr, $prev) {
            if ($prev == 0) {
                return $curr > 0 ? '+100.0%' : '0.0%';
            }
            $change = (($curr - $prev) / $prev) * 100;
            return ($change >= 0 ? '+' : '') . number_format($change, 1) . '%';
        };

        $revenueChange = $getPercentChange($current['revenue'], $previous['revenue']);
        $revenueUp = $current['revenue'] >= $previous['revenue'];

        $ordersChange = $getPercentChange($current['orders'], $previous['orders']);
        $ordersUp = $current['orders'] >= $previous['orders'];

        $avgChange = $getPercentChange($current['avg'], $previous['avg']);
        $avgUp = $current['avg'] >= $previous['avg'];

        $satDiff = $current['satisfaction'] - $previous['satisfaction'];
        $satChange = ($satDiff >= 0 ? '+' : '') . number_format($satDiff, 1) . '%';
        $satUp = $satDiff >= 0;

        $stats = [
            'monthly_revenue' => [
                'value' => 'Rp ' . number_format($current['revenue'], 0, ',', '.'),
                'change' => $revenueChange,
                'up' => $revenueUp,
                'sub' => 'Rp ' . number_format($previous['revenue'], 0, ',', '.') . ' periode sebelumnya'
            ],
            'orders_served' => [
                'value' => number_format($current['orders'], 0, ',', '.'),
                'change' => $ordersChange,
                'up' => $ordersUp,
                'sub' => number_format($previous['orders'], 0, ',', '.') . ' periode sebelumnya'
            ],
            'avg_per_order' => [
                'value' => 'Rp ' . number_format($current['avg'], 0, ',', '.'),
                'change' => $avgChange,
                'up' => $avgUp,
                'sub' => 'vs Rp ' . number_format($previous['avg'], 0, ',', '.') . ' periode sebelumnya'
            ],
            'satisfaction' => [
                'value' => number_format($current['satisfaction'], 1) . '%',
                'change' => $satChange,
                'up' => $satUp,
                'sub' => 'dari ' . number_format($current['reviews_count'], 0, ',', '.') . ' ulasan'
            ]
        ];

        // 12-month Trend data (ending at current filter date)
        $months = [];
        $revenueData = [];
        $volumeData = [];
        $targetRevenue = [35, 35, 38, 38, 42, 42, 44, 44, 46, 46, 48, 48]; // standard targets
        $targetVolume = [200, 200, 220, 220, 240, 240, 260, 260, 280, 280, 300, 300]; // standard volume targets

        $targetDate = \Carbon\Carbon::parse($to)->startOfMonth();
        $monthMap = [
            'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
            'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
        ];

        for ($i = 11; $i >= 0; $i--) {
            $m = $targetDate->copy()->subMonths($i);
            $monthName = $monthMap[$m->format('M')] ?? $m->format('M');
            $months[] = $monthName;

            $mStart = $m->copy()->startOfMonth()->toDateTimeString();
            $mEnd = $m->copy()->endOfMonth()->toDateTimeString();

            $mRevenueQuery = Payment::where('status', 'paid');
            $paymentDateFilter($mRevenueQuery, $mStart, $mEnd);
            if ($outletId) {
                $mRevenueQuery->whereHas('booking', function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                });
            }
            $totalRev = $mRevenueQuery->sum('amount');
            $revenueData[] = round($totalRev / 1000000, 1);

            $mVolumeQuery = Booking::where('status', 'completed');
            $completedDateFilter($mVolumeQuery, $mStart, $mEnd);
            if ($outletId) {
                $mVolumeQuery->where('outlet_id', $outletId);
            }
            $volumeData[] = $mVolumeQuery->count();
        }

        // Service Type Distribution
        $serviceQuery = Booking::where('status', 'completed');
        $completedDateFilter($serviceQuery, $from, $to);
        if ($outletId) {
            $serviceQuery->where('outlet_id', $outletId);
        }
        $servicesRaw = $serviceQuery->selectRaw('package_id, count(*) as count')
            ->groupBy('package_id')
            ->with('package')
            ->get();

        $totalServicesCount = $servicesRaw->sum('count');
        $colors = ['#1B2337', '#F0C419', '#3B82F6', '#10B981', '#94A3B8', '#A78BFA', '#F472B6'];
        $serviceDistribution = [];

        $servicesRaw = $servicesRaw->sortByDesc('count');
        $idx = 0;
        foreach ($servicesRaw as $s) {
            $packageName = $s->package ? $s->package->name : ($s->service_type === 'home' ? 'Home Detailing' : 'Standard Wash');
            $pct = $totalServicesCount > 0 ? ($s->count / $totalServicesCount) * 100 : 0;
            $color = $colors[$idx % count($colors)];
            $serviceDistribution[] = [
                'label' => $packageName,
                'count' => $s->count,
                'pct' => round($pct, 1) . '%',
                'pct_val' => round($pct, 1),
                'color' => $color
            ];
            $idx++;
        }

        if (empty($serviceDistribution)) {
            $serviceDistribution = [
                ['label' => 'Premium Full Wash', 'count' => 0, 'pct' => '40.0%', 'pct_val' => 40.0, 'color' => '#1B2337'],
                ['label' => 'Standard Exterior', 'count' => 0, 'pct' => '33.0%', 'pct_val' => 33.0, 'color' => '#F0C419'],
                ['label' => 'Home Detailing', 'count' => 0, 'pct' => '16.0%', 'pct_val' => 16.0, 'color' => '#3B82F6'],
                ['label' => 'LS Priority Club', 'count' => 0, 'pct' => '5.0%', 'pct_val' => 5.0, 'color' => '#10B981'],
                ['label' => 'Lainnya', 'count' => 0, 'pct' => '6.0%', 'pct_val' => 6.0, 'color' => '#94A3B8'],
            ];
        }

        // Outlet Performance Comparison
        $outletPerformance = [];
        $outletsQuery = Outlet::where('status', 'active');
        if ($outletId) {
            $outletsQuery->where('id', $outletId);
        }
        $activeOutletsForPerf = $outletsQuery->get();

        foreach ($activeOutletsForPerf as $outlet) {
            $outletRevQuery = Payment::where('status', 'paid')
                ->whereHas('booking', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                });
            $paymentDateFilter($outletRevQuery, $from, $to);
            $outletRev = $outletRevQuery->sum('amount');

            $outletOrdersQuery = Booking::where('status', 'completed')
                ->where('outlet_id', $outlet->id);
            $completedDateFilter($outletOrdersQuery, $from, $to);
            $outletOrders = $outletOrdersQuery->count();

            $outletPerformance[] = [
                'name' => $outlet->name,
                'revenue_jt' => round($outletRev / 1000000, 1),
                'orders' => $outletOrders
            ];
        }

        usort($outletPerformance, function($a, $b) {
            return $b['revenue_jt'] <=> $a['revenue_jt'];
        });

        // Technician Leaderboard
        $techniciansQuery = Booking::where('status', 'completed')
            ->whereNotNull('technician_id');
        $completedDateFilter($techniciansQuery, $from, $to);
        if ($outletId) {
            $techniciansQuery->where('outlet_id', $outletId);
        }

        $techStats = $techniciansQuery->selectRaw('technician_id, count(*) as count')
            ->groupBy('technician_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $leaderboard = [];
        $medals = ['🥇', '🥈', '🥉', '4', '5'];

        foreach ($techStats as $index => $stat) {
            $tech = Technician::find($stat->technician_id);
            if ($tech) {
                $avgRating = Review::where('technician_id', $tech->id)
                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                    ->avg('rating') ?? $tech->rating ?? 5.0;

                $leaderboard[] = [
                    'name' => $tech->name,
                    'avatar' => $tech->avatar,
                    'orders' => $stat->count,
                    'rating' => round($avgRating, 1),
                    'medal' => $medals[$index] ?? ($index + 1)
                ];
            }
        }

        if (count($leaderboard) === 0) {
            $topTechsQuery = Technician::where('status', 'active');
            if ($outletId) {
                $topTechsQuery->where('outlet_id', $outletId);
            }
            $topTechs = $topTechsQuery->orderBy('rating', 'desc')
                ->orderBy('total_orders', 'desc')
                ->limit(5)
                ->get();

            foreach ($topTechs as $index => $tech) {
                $leaderboard[] = [
                    'name' => $tech->name,
                    'avatar' => $tech->avatar,
                    'orders' => $tech->total_orders,
                    'rating' => round($tech->rating, 1),
                    'medal' => $medals[$index] ?? ($index + 1)
                ];
            }
        }

        // Recent Payments / Transactions
        $recentPaymentsQuery = Payment::with(['booking.customer', 'booking.package']);
        $paymentDateFilter($recentPaymentsQuery, $from, $to);
        if ($outletId) {
            $recentPaymentsQuery->whereHas('booking', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            });
        }
        $recentPayments = $recentPaymentsQuery->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'stats',
            'outlets',
            'from',
            'to',
            'months',
            'revenueData',
            'volumeData',
            'targetRevenue',
            'targetVolume',
            'serviceDistribution',
            'outletPerformance',
            'leaderboard',
            'recentPayments'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->get('format','pdf');
        if ($format === 'pdf') {
            // barryvdh/laravel-dompdf
            return response()->json(['message'=>'Install barryvdh/laravel-dompdf: composer require barryvdh/laravel-dompdf']);
        }
        // maatwebsite/excel
        return response()->json(['message'=>'Install maatwebsite/excel: composer require maatwebsite/excel']);
    }
}
