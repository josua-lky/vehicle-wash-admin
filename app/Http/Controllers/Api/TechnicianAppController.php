<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TechnicianAppController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $technician = Technician::where('email', $request->email)->first();

        if (!$technician || !Hash::check($request->password, $technician->password)) {
            return response()->json([
                'message' => 'Email atau kata sandi salah.'
            ], 401);
        }

        $token = $technician->createToken('technician')->plainTextToken;

        return response()->json([
            'user' => $technician->load('outlet'),
            'token' => $token,
            'role' => 'technician'
        ]);
    }

    public function bookings(Request $request)
    {
        $technician = $request->user();
        
        $bookings = Booking::where('technician_id', $technician->id)
            ->with(['customer', 'package', 'review', 'vehicle'])
            ->latest('scheduled_at')
            ->get();

        return response()->json($bookings);
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Ensure this technician owns the booking
        if ($booking->technician_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,on_way,in_progress,completed',
            'before_photo' => 'nullable|image|max:5120',
            'after_photo' => 'nullable|image|max:5120',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'in_progress') {
            if ($request->hasFile('before_photo')) {
                $path = $request->file('before_photo')->store('bookings', 'public');
                $updateData['before_photo'] = $path;
            }
        } elseif ($validated['status'] === 'completed') {
            if ($request->hasFile('before_photo')) {
                $path = $request->file('before_photo')->store('bookings', 'public');
                $updateData['before_photo'] = $path;
            }
            if ($request->hasFile('after_photo')) {
                $path = $request->file('after_photo')->store('bookings', 'public');
                $updateData['after_photo'] = $path;
            }
            $updateData['completed_at'] = now();
        }

        $booking->update($updateData);

        if ($validated['status'] === 'completed') {
            // Update technician rating and total orders count
            $request->user()->updateRating();
        }

        return response()->json([
            'success' => true,
            'booking' => $booking->load(['customer', 'package', 'review', 'vehicle'])
        ]);
    }

    public function updateLocation(Request $request)
    {
        $technician = $request->user();

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $technician->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi teknisi berhasil diperbarui.'
        ]);
    }

    public function getChatMessages($bookingId)
    {
        $messages = ChatMessage::where('booking_id', $bookingId)
            ->oldest()
            ->get();

        return response()->json($messages);
    }

    public function sendChatMessage(Request $request, $bookingId)
    {
        $validated = $request->validate([
            'sender_type' => 'required|in:customer,technician',
            'message' => 'required|string'
        ]);

        $message = ChatMessage::create([
            'booking_id' => $bookingId,
            'sender_type' => $validated['sender_type'],
            'message' => $validated['message']
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
