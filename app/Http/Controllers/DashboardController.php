<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $monthlyBookings = Booking::selectRaw('MONTH(scheduled_at) as month, COUNT(*) as count')
            ->whereYear('scheduled_at', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = $monthlyBookings[$m] ?? 0;
        }

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyBookings = Booking::selectRaw('DAYOFWEEK(scheduled_at) as day, COUNT(*) as count')
            ->whereBetween('scheduled_at', [$startOfWeek, $endOfWeek])
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $daysOfWeek = [2, 3, 4, 5, 6, 7, 1]; // Mon to Sun
        $weeklyData = [];
        foreach ($daysOfWeek as $day) {
            $weeklyData[] = $weeklyBookings[$day] ?? 0;
        }

        $todayBookings = Booking::whereDate('scheduled_at', today())->get();

        $yesterdayBookingsCount = Booking::whereDate('scheduled_at', today()->subDay())->count();
        $todayBookingsCount = $todayBookings->count();
        if ($yesterdayBookingsCount > 0) {
            $bookingsTodayPct = round((($todayBookingsCount - $yesterdayBookingsCount) / $yesterdayBookingsCount) * 100, 1);
        } else {
            $bookingsTodayPct = $todayBookingsCount > 0 ? 100 : 0;
        }

        $inactiveClients = Customer::whereIn('status', ['inactive', 'banned'])->count();

        $lastMonthRevenue = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');
        $thisMonthRevenue = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
        if ($lastMonthRevenue > 0) {
            $revenueChangePct = round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } else {
            $revenueChangePct = $thisMonthRevenue > 0 ? 100 : 0;
        }

        $totalActiveTechs = Technician::where('status', 'active')->count();
        $busyTechs = Technician::where('status', 'active')
            ->whereHas('bookings', function($q) {
                $q->whereIn('status', ['assigned', 'on_way', 'in_progress']);
            })->count();
        $techUtilization = $totalActiveTechs > 0 ? round(($busyTechs / $totalActiveTechs) * 100) : 0;

        $stats = [
            'bookings_today'     => $todayBookingsCount,
            'bookings_today_pct' => $bookingsTodayPct,
            'active_clients'     => Customer::where('status', 'active')->count(),
            'inactive_clients'   => $inactiveClients,
            'total_revenue'      => $thisMonthRevenue,
            'revenue_change_pct' => $revenueChangePct,
            'active_technicians' => $totalActiveTechs,
            'tech_utilization'   => $techUtilization,
            'perf_total'         => $todayBookingsCount,
            'perf_completed'     => $todayBookings->where('status', 'completed')->count(),
            'perf_in_progress'   => $todayBookings->whereIn('status', ['in_progress', 'assigned', 'on_way'])->count(),
            'perf_pending'       => $todayBookings->whereIn('status', ['pending', 'confirmed'])->count(),
            'perf_cancelled'     => $todayBookings->where('status', 'cancelled')->count(),
        ];

        $recentBookings = Booking::with('customer')->latest()->limit(6)->get()
            ->map(fn($b) => [
                'id'       => $b->id, 'code'     => $b->booking_code,
                'customer' => $b->customer->name ?? '—',
                'vehicle'  => $b->vehicle_name   ?? '—',
                'status'   => $b->status,
                'time'     => optional($b->scheduled_at)->format('d M, H:i') ?? '—',
            ]);

        return view('dashboard.index', compact('stats', 'recentBookings', 'monthlyData', 'weeklyData'));
    }

    public function search(Request $request)
    {
        $q = $request->q;
        if (empty($q)) {
            return redirect('/dashboard');
        }

        $bookings = Booking::with('customer')
            ->where('booking_code', 'like', "%{$q}%")
            ->orWhere('vehicle_name', 'like', "%{$q}%")
            ->orWhereHas('customer', fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))
            ->latest()->limit(10)->get();

        $technicians = Technician::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->latest()->limit(10)->get();

        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->latest()->limit(10)->get();

        return view('dashboard.search', compact('q', 'bookings', 'technicians', 'customers'));
    }
}
