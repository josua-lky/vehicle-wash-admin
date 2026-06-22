<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Package;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'technician', 'package'])
            ->when($request->status,       fn($q) => $q->where('status', $request->status))
            ->when($request->service_type, fn($q) => $q->where('service_type', $request->service_type))
            ->when($request->date_from,    fn($q) => $q->whereDate('scheduled_at', '>=', $request->date_from))
            ->when($request->date_to,      fn($q) => $q->whereDate('scheduled_at', '<=', $request->date_to))
            ->when($request->vehicle_type, function ($q) use ($request) {
                if ($request->vehicle_type === 'motor') {
                    $q->whereIn('vehicle_type', ['roda_2', 'motor']);
                } elseif ($request->vehicle_type === 'mobil') {
                    $q->whereIn('vehicle_type', ['roda_4', 'mobil']);
                }
            })
            ->when($request->search, function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->where('booking_code', 'like', "%{$request->search}%")
                        ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$request->search}%")
                                                                ->orWhere('phone', 'like', "%{$request->search}%"));
                });
            })
            ->latest();

        $bookings = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => Booking::count(),
            'completed'   => Booking::where('status', 'completed')->count(),
            'in_progress' => Booking::whereIn('status', ['in_progress', 'assigned', 'on_way'])->count(),
            'cancelled'   => Booking::where('status', 'cancelled')->count(),
        ];

        // 7-day booking trends
        $trendLabels = [];
        $trendData = [];
        $dayLabelsMap = [
            0 => 'Min',
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab'
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayOfWeek = $date->dayOfWeek;
            $trendLabels[] = $dayLabelsMap[$dayOfWeek];
            $trendData[] = Booking::whereDate('scheduled_at', $date->toDateString())->count();
        }

        // Service type breakdown
        $homeCount = Booking::where('service_type', 'home')->count();
        $outletCount = Booking::where('service_type', 'outlet')->count();
        $totalServices = $homeCount + $outletCount;
        $homePct = $totalServices > 0 ? round(($homeCount / $totalServices) * 100) : 0;
        $outletPct = $totalServices > 0 ? round(($outletCount / $totalServices) * 100) : 0;

        return view('bookings.index', compact(
            'bookings',
            'stats',
            'trendLabels',
            'trendData',
            'homeCount',
            'outletCount',
            'homePct',
            'outletPct'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load(['customer', 'technician', 'package', 'payment', 'outlet']);
        $technicians = Technician::whereIn('status', ['active', 'busy'])->get();
        return view('bookings.show', compact('booking', 'technicians'));
    }

    public function create()
    {
        $customers   = Customer::where('status', 'active')->get();
        $technicians = Technician::whereIn('status', ['active', 'busy'])->get();
        $packages    = Package::where('is_active', true)->get();
        $outlets     = Outlet::where('status', 'active')->get();
        return view('bookings.create', compact('customers', 'technicians', 'packages', 'outlets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'package_id'    => 'required|exists:packages,id',
            'service_type'  => 'required|in:home,outlet',
            'scheduled_at'  => 'required|date',
            'outlet_id'     => 'nullable|exists:outlets,id',
            'outlet_slot_id'=> 'nullable|exists:wash_slots,id',
            'service_address' => 'nullable|string',
            'vehicle_name'  => 'required|string|max:100',
            'vehicle_type'  => 'required|in:roda_2,roda_4',
            'notes'         => 'nullable|string|max:500',
        ]);

        $defaultAddress = \App\Models\UserAddress::where('customer_id', $data['customer_id'])
            ->where('is_default', true)
            ->first();
        $data['latitude'] = $defaultAddress ? $defaultAddress->latitude : null;
        $data['longitude'] = $defaultAddress ? $defaultAddress->longitude : null;

        $outletId = $data['outlet_id'] ?? null;
        $outletSlotId = $data['outlet_slot_id'] ?? null;

        if ($data['service_type'] === 'outlet' && !empty($outletId)) {
            if (empty($outletSlotId)) {
                $slotDate = date('Y-m-d', strtotime($data['scheduled_at']));
                $slotTime = date('H:i:00', strtotime($data['scheduled_at']));
                
                $slot = \App\Models\WashSlot::firstOrCreate(
                    [
                        'outlet_id' => $outletId,
                        'slot_date' => $slotDate,
                        'slot_time' => $slotTime,
                    ],
                    [
                        'capacity' => \App\Models\Outlet::find($outletId)->capacity_per_hour ?? 3,
                        'booked_count' => 0,
                        'status' => 'available',
                    ]
                );
                $outletSlotId = $slot->id;
            } else {
                $slot = \App\Models\WashSlot::find($outletSlotId);
            }

            if ($slot) {
                if ($slot->booked_count >= $slot->capacity || $slot->status === 'blocked') {
                    return back()->withErrors(['scheduled_at' => 'Slot waktu yang Anda pilih sudah penuh.'])->withInput();
                }
                $slot->increment('booked_count');
            }
        }

        $package = Package::find($data['package_id']);
        $data['booking_code']  = 'VW-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $data['status']        = 'pending';
        $data['subtotal']      = $package->price;
        $data['discount_amount'] = 0;
        $data['total_amount']  = $package->price;
        $data['outlet_slot_id'] = $outletSlotId;

        $booking = Booking::create($data);

        \App\Models\PushNotification::notifyAdmin(
            'new_booking',
            'Booking Baru',
            "Pemesanan baru {$booking->booking_code} telah dibuat.",
            ['booking_id' => $booking->id]
        );

        // If from Wash Slots page, redirect back with success instead of show page to make UI flow smooth
        if ($request->has('from_slots')) {
            return back()->with('success', "Booking {$booking->booking_code} berhasil dibuat.");
        }

        return redirect("/bookings/{$booking->id}")
            ->with('success', "Booking {$booking->booking_code} berhasil dibuat.");
    }

    public function complete(Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect('/bookings')->with('error', 'Tidak dapat mengubah pesanan yang sudah selesai atau dibatalkan.');
        }
        if ($booking->status === 'pending') {
            return redirect('/bookings')->with('error', 'Pesanan harus dikonfirmasi terlebih dahulu sebelum diselesaikan.');
        }
        $booking->update(['status' => 'completed']);
        if ($booking->technician) {
            $booking->technician->update(['status' => 'active']);
            $booking->technician->updateRating();
        }
        if ($booking->outlet_slot_id) {
            $slot = \App\Models\WashSlot::find($booking->outlet_slot_id);
            if ($slot && $slot->booked_count > 0) {
                $slot->decrement('booked_count');
            }
        }
        return back()->with('success', "Booking {$booking->booking_code} berhasil diselesaikan.");
    }

    public function destroy(Booking $booking)
    {
        if ($booking->outlet_slot_id) {
            $slot = \App\Models\WashSlot::find($booking->outlet_slot_id);
            if ($slot && $slot->booked_count > 0) {
                $slot->decrement('booked_count');
            }
        }
        $booking->delete();
        return redirect('/bookings')->with('success', 'Booking berhasil dihapus.');
    }

    public function confirm(Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect('/bookings')->with('error', 'Tidak dapat mengubah pesanan yang sudah selesai atau dibatalkan.');
        }
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', "Booking {$booking->booking_code} dikonfirmasi.");
    }

    public function cancel(Request $request, Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect('/bookings')->with('error', 'Tidak dapat mengubah pesanan yang sudah selesai atau dibatalkan.');
        }
        if ($booking->status !== 'cancelled') {
            $booking->update(['status' => 'cancelled', 'cancelled_reason' => $request->reason]);
            if ($booking->technician && $booking->technician->status === 'busy') {
                $hasOtherInProgress = Booking::where('technician_id', $booking->technician_id)
                    ->where('status', 'in_progress')
                    ->where('id', '!=', $booking->id)
                    ->exists();
                if (!$hasOtherInProgress) {
                    $booking->technician->update(['status' => 'active']);
                }
            }
            \App\Models\PushNotification::notifyAdmin(
                'booking_cancelled',
                'Booking Dibatalkan',
                "Pemesanan {$booking->booking_code} telah dibatalkan.",
                ['booking_id' => $booking->id]
            );
            if ($booking->outlet_slot_id) {
                $slot = \App\Models\WashSlot::find($booking->outlet_slot_id);
                if ($slot && $slot->booked_count > 0) {
                    $slot->decrement('booked_count');
                }
            }
        }
        return back()->with('success', "Booking {$booking->booking_code} dibatalkan.");
    }

    public function assign(Request $request, Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect('/bookings')->with('error', 'Tidak dapat mengubah pesanan yang sudah selesai atau dibatalkan.');
        }
        $request->validate(['technician_id' => 'required|exists:technicians,id']);
        $booking->update(['technician_id' => $request->technician_id, 'status' => 'assigned']);
        return back()->with('success', 'Teknisi berhasil ditugaskan.');
    }

    public function export(Request $request)
    {
        $format   = $request->get('format', 'excel');
        $bookings = Booking::with(['customer', 'package'])->latest()->get();
        // Requires maatwebsite/excel or barryvdh/laravel-dompdf
        return response()->json(['message' => 'Export feature — install maatwebsite/excel or barryvdh/laravel-dompdf']);
    }
}
