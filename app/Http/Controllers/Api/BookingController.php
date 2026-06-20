<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Vehicle;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->user();

        $bookings = Booking::with([
            'package',
            'payment',
            'vehicle',
            'promo',
            'technician',
            'review'
        ])

        ->where(
            'customer_id',
            $customer->id
        )

        ->latest()

        ->get();

        return response()->json(
            $bookings
        );
    }

    public function store(Request $request)
    {
        $customer = $request->user();

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'package_id' => 'required|exists:packages,id',
            'service_type' => 'required|in:home,outlet',
            'scheduled_at' => 'required|date',
            'service_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'promo_code' => 'nullable|string',
            'technician_id' => 'nullable|exists:technicians,id',
            'outlet_id' => 'nullable|exists:outlets,id'
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $package = Package::findOrFail($validated['package_id']);

        $promo = null;
        $discount = 0;

        if (!empty($validated['promo_code'])) {
            $promo = \App\Models\Promo::where('code', strtoupper($validated['promo_code']))
                ->where('status', 'active')
                ->first();

            if (!$promo) {
                return response()->json(['message' => 'Kode promo tidak valid atau sudah kedaluwarsa.'], 422);
            }

            if ($promo->starts_at && $promo->starts_at > now()) {
                return response()->json(['message' => 'Promo belum dimulai.'], 422);
            }
            if ($promo->expires_at && $promo->expires_at < now()) {
                return response()->json(['message' => 'Promo sudah kedaluwarsa.'], 422);
            }

            if ($promo->max_usage && $promo->used_count >= $promo->max_usage) {
                return response()->json(['message' => 'Batas penggunaan promo telah habis.'], 422);
            }

            // Check usage limit per user
            $usageCount = \App\Models\PromoUsage::where('customer_id', $customer->id)
                ->where('promo_id', $promo->id)
                ->count();
            if ($usageCount >= ($promo->max_usage_per_user ?? 1)) {
                return response()->json(['message' => 'Anda sudah mengklaim promo ini.'], 422);
            }

            if ($promo->min_transaction && $package->price < $promo->min_transaction) {
                return response()->json(['message' => 'Minimal transaksi untuk promo ini adalah Rp ' . number_format($promo->min_transaction, 0, ',', '.')], 422);
            }

            // Check if promo is for new customer / first wash (e.g. code contains 'FIRST' or description contains 'baru' or 'pertama')
            if (str_contains(strtoupper($promo->code), 'FIRST') || str_contains(strtolower($promo->description), 'baru') || str_contains(strtolower($promo->description), 'pertama')) {
                $pastBookingsCount = \App\Models\Booking::where('customer_id', $customer->id)
                    ->where('status', '!=', 'cancelled')
                    ->count();
                if ($pastBookingsCount > 0) {
                    return response()->json(['message' => 'Promo ini hanya berlaku untuk pemesanan pertama (pelanggan baru).'], 422);
                }
            }

            // Specific validation for FIRST30 (First Detailing 30% Discount)
            if (strtoupper($promo->code) === 'FIRST30') {
                if (!str_contains(strtolower($package->name), 'detail')) {
                    return response()->json(['message' => 'Promo ini hanya berlaku untuk paket Detailing.'], 422);
                }
            }

            $discount = (float) $promo->calculateDiscount($package->price);
        }

        $outletId = $validated['outlet_id'] ?? null;
        if (empty($outletId) && !empty($validated['technician_id'])) {
            $tech = \App\Models\Technician::find($validated['technician_id']);
            if ($tech) {
                $outletId = $tech->outlet_id;
            }
        }
        if (empty($outletId) && !empty($validated['service_address'])) {
            $outlet = \App\Models\Outlet::where('address', $validated['service_address'])->first();
            if ($outlet) {
                $outletId = $outlet->id;
            }
        }

        $defaultAddress = \App\Models\UserAddress::where('customer_id', $customer->id)
            ->where('is_default', true)
            ->first();
        $latitude = $defaultAddress ? $defaultAddress->latitude : null;
        $longitude = $defaultAddress ? $defaultAddress->longitude : null;
        if (!empty($validated['technician_id'])) {
            $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at'])->format('Y-m-d H:i:s');
            $isTechnicianBooked = Booking::where('technician_id', $validated['technician_id'])
                ->where('scheduled_at', $scheduledAt)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->exists();
            if ($isTechnicianBooked) {
                return response()->json(['message' => 'Teknisi pilihan Anda sudah memiliki jadwal di jam tersebut. Silakan pilih teknisi lain.'], 422);
            }
        }

        $outletSlotId = null;
        if (!empty($outletId)) {
            $slotDate = date('Y-m-d', strtotime($validated['scheduled_at']));
            $slotTime = date('H:i:00', strtotime($validated['scheduled_at']));
            
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
            
            if ($slot->booked_count >= $slot->capacity || $slot->status === 'blocked') {
                return response()->json(['message' => 'Slot waktu yang Anda pilih sudah penuh.'], 422);
            }
            
            $slot->increment('booked_count');
            $outletSlotId = $slot->id;
        }

        $booking = Booking::create([
            'booking_code' => 'VW-' . strtoupper(substr(uniqid(), -6)),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->brand . ' ' . $vehicle->model,
            'vehicle_type' => $vehicle->type,
            'package_id' => $package->id,
            'service_type' => $validated['service_type'],
            'service_address' => $validated['service_address'] ?? null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'pending',
            'promo_id' => $promo ? $promo->id : null,
            'subtotal' => $package->price,
            'discount_amount' => $discount,
            'total_amount' => max(0, $package->price - $discount),
            'notes' => $validated['notes'] ?? null,
            'technician_id' => $validated['technician_id'] ?? null,
            'outlet_id' => $outletId,
            'outlet_slot_id' => $outletSlotId
        ]);

        \App\Models\Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'ewallet',
            'payment_provider' => 'onopay',
            'transaction_id' => 'TX-' . strtoupper(substr(uniqid(), -8)),
            'status' => 'paid',
            'amount' => $booking->total_amount,
            'paid_at' => now()
        ]);

        \App\Models\PushNotification::notifyAdmin(
            'new_booking',
            'Booking Baru',
            "Pemesanan baru {$booking->booking_code} telah dibuat.",
            ['booking_id' => $booking->id]
        );

        \App\Models\PushNotification::notifyAdmin(
            'payment_received',
            'Pembayaran Diterima',
            "Pembayaran sebesar Rp " . number_format($booking->total_amount, 0, ',', '.') . " diterima untuk pemesanan {$booking->booking_code}.",
            ['booking_id' => $booking->id]
        );

        if ($promo) {
            \App\Models\PromoUsage::create([
                'promo_id' => $promo->id,
                'customer_id' => $customer->id,
                'booking_id' => $booking->id,
                'discount_applied' => $discount
            ]);
            $promo->increment('used_count');
        }

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'booking' => $booking
        ]);
    }

    public function show(
        Request $request,
        $id
    )
    {
        $customer = $request->user();

        $booking = Booking::with([

            'package',
            'payment',
            'review',
            'vehicle',
            'technician'

        ])

        ->where(
            'customer_id',
            $customer->id
        )

        ->findOrFail($id);

        return response()->json(
            $booking
        );
    }

    public function cancel(Request $request, $id)
    {
        $customer = $request->user();

        $booking = Booking::where('customer_id', $customer->id)->findOrFail($id);

        if ($booking->status !== 'cancelled') {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_reason' => $request->reason ?: 'Dibatalkan oleh pelanggan'
            ]);

            $payment = $booking->payment;
            if ($payment && $payment->status === 'paid') {
                $payment->update([
                    'refund_requested' => true
                ]);
            }

            \App\Models\PushNotification::notifyAdmin(
                'refund_requested',
                'Permintaan Refund',
                "Pelanggan membatalkan pemesanan {$booking->booking_code} dan meminta pengembalian dana sebesar Rp " . number_format($booking->total_amount, 0, ',', '.') . ".",
                ['booking_id' => $booking->id]
            );

            if ($booking->outlet_slot_id) {
                $slot = \App\Models\WashSlot::find($booking->outlet_slot_id);
                if ($slot && $slot->booked_count > 0) {
                    $slot->decrement('booked_count');
                }
            }
        }

        return response()->json([
            'message' => 'Booking dibatalkan dan permintaan refund telah dikirim ke admin.'
        ]);
    }

    public function submitReview(Request $request, $id)
    {
        $customer = $request->user();
        
        $booking = Booking::where('customer_id', $customer->id)->findOrFail($id);
        
        if ($booking->status !== 'completed') {
            return response()->json(['message' => 'Hanya pesanan yang sudah selesai yang dapat diulas.'], 422);
        }
        
        if ($booking->review()->exists()) {
            return response()->json(['message' => 'Anda sudah mengulas pesanan ini.'], 422);
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);
        
        $review = \App\Models\Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'technician_id' => $booking->technician_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null
        ]);

        if ($review->rating < 3) {
            \App\Models\PushNotification::notifyAdmin(
                'bad_rating',
                'Rating Buruk',
                "Rating buruk ({$review->rating} bintang) diterima dari pelanggan {$customer->name} untuk pemesanan {$booking->booking_code}.",
                ['booking_id' => $booking->id, 'review_id' => $review->id]
            );
        }
        
        if ($booking->technician) {
            $booking->technician->updateRating();
        }
        
        return response()->json([
            'message' => 'Ulasan berhasil disimpan',
            'review' => $review
        ]);
    }
}