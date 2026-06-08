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

        return view('bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['customer', 'technician', 'package', 'payment', 'outlet']);
        return view('bookings.show', compact('booking'));
    }

    public function create()
    {
        $customers   = Customer::where('status', 'active')->get();
        $technicians = Technician::where('status', 'active')->get();
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

        if ($request->outlet_slot_id) {
            $slot = \App\Models\WashSlot::find($request->outlet_slot_id);
            if ($slot && $slot->booked_count >= $slot->capacity) {
                return back()->withErrors(['outlet_slot_id' => 'Slot ini sudah penuh.'])->withInput();
            }
        }

        $package = Package::find($request->package_id);
        $data['booking_code']  = 'VW-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $data['status']        = 'pending';
        $data['subtotal']      = $package->price;
        $data['discount_amount'] = 0;
        $data['total_amount']  = $package->price;

        $booking = Booking::create($data);

        if ($booking->outlet_slot_id) {
            $slot = \App\Models\WashSlot::find($booking->outlet_slot_id);
            if ($slot) {
                $slot->increment('booked_count');
            }
        }

        // If from Wash Slots page, redirect back with success instead of show page to make UI flow smooth
        if ($request->has('from_slots')) {
            return back()->with('success', "Booking {$booking->booking_code} berhasil dibuat.");
        }

        return redirect("/bookings/{$booking->id}")
            ->with('success', "Booking {$booking->booking_code} berhasil dibuat.");
    }

    public function edit(Booking $booking)
    {
        $technicians = Technician::where('status', 'active')->get();
        return view('bookings.edit', compact('booking', 'technicians'));
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status'        => 'required|in:pending,confirmed,completed,cancelled',
            'notes'         => 'nullable|string|max:500',
        ]);
        $booking->update($data);
        return back()->with('success', 'Booking berhasil diperbarui.');
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
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', "Booking {$booking->booking_code} dikonfirmasi.");
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->status !== 'cancelled') {
            $booking->update(['status' => 'cancelled', 'cancelled_reason' => $request->reason]);
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
