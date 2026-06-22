<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Package;
use App\Models\Technician;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BookingOutletTest extends TestCase
{
    use DatabaseTransactions;

    public function test_booking_creation_with_outlet_id_directly()
    {
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $package = Package::first();
        $outlet = Outlet::first();

        $payload = [
            'vehicle_id' => $vehicle->id,
            'package_id' => $package->id,
            'service_type' => 'outlet',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'service_address' => $outlet->address,
            'outlet_id' => $outlet->id,
        ];

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson('/api/bookings', $payload);

        $response->assertStatus(200);
        
        $bookingId = $response->json('booking.id');
        $booking = Booking::find($bookingId);
        
        $this->assertEquals($outlet->id, $booking->outlet_id);
    }

    public function test_booking_creation_resolves_outlet_from_technician()
    {
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $package = Package::first();
        $tech = Technician::first();

        $payload = [
            'vehicle_id' => $vehicle->id,
            'package_id' => $package->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'service_address' => 'Jl. Test No. 10',
            'technician_id' => $tech->id,
        ];

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson('/api/bookings', $payload);

        $response->assertStatus(200);
        
        $bookingId = $response->json('booking.id');
        $booking = Booking::find($bookingId);
        
        $this->assertEquals($tech->outlet_id, $booking->outlet_id);
    }

    public function test_booking_observer_updates_outlet_id_on_technician_assignment()
    {
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $package = Package::first();

        // Create booking without technician and without outlet
        $booking = Booking::create([
            'booking_code' => 'VW-TEST-OBS',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->brand . ' ' . $vehicle->model,
            'vehicle_type' => $vehicle->type,
            'package_id' => $package->id,
            'service_type' => 'home',
            'service_address' => 'Jl. Test No. 10',
            'scheduled_at' => now()->addDay(),
            'status' => 'pending',
            'subtotal' => $package->price,
            'total_amount' => $package->price,
        ]);

        $this->assertNull($booking->outlet_id);

        // Assign a technician
        $tech = Technician::first();
        $booking->update([
            'technician_id' => $tech->id,
            'status' => 'assigned'
        ]);

        $booking->refresh();
        $this->assertEquals($tech->outlet_id, $booking->outlet_id);
    }

    public function test_booking_creation_creates_or_increments_wash_slot()
    {
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $package = Package::first();
        $outlet = Outlet::first();

        $scheduledAt = now()->addDays(2)->setHour(10)->setMinute(0)->setSecond(0);

        $payload = [
            'vehicle_id' => $vehicle->id,
            'package_id' => $package->id,
            'service_type' => 'outlet',
            'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
            'service_address' => $outlet->address,
            'outlet_id' => $outlet->id,
        ];

        $slotDate = $scheduledAt->toDateString();
        $slotTime = '10:00:00';
        
        $slot = \App\Models\WashSlot::where('outlet_id', $outlet->id)
            ->whereDate('slot_date', $slotDate)
            ->whereTime('slot_time', $slotTime)
            ->first();
            
        $initialBooked = $slot ? $slot->booked_count : 0;

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson('/api/bookings', $payload);

        $response->assertStatus(200);
        
        $bookingId = $response->json('booking.id');
        $booking = Booking::find($bookingId);
        
        $this->assertNotNull($booking->outlet_slot_id);
        
        $slotAfter = \App\Models\WashSlot::find($booking->outlet_slot_id);
        $this->assertEquals($initialBooked + 1, $slotAfter->booked_count);

        // Test cancellation decrements slot
        $responseCancel = $this->actingAs($customer, 'sanctum')
            ->putJson("/api/bookings/{$bookingId}/cancel", ['reason' => 'Test cancel']);
        $responseCancel->assertStatus(200);

        $slotAfterCancel = \App\Models\WashSlot::find($booking->outlet_slot_id);
        $this->assertEquals($initialBooked, $slotAfterCancel->booked_count);
    }

    public function test_booking_creation_fails_when_slot_capacity_is_exceeded()
    {
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $package = Package::first();
        $outlet = Outlet::first();
        $scheduledAt = now()->addDays(2)->setTime(11, 0, 0);

        // First, create or update a slot with capacity 1 and booked_count 1
        $slot = \App\Models\WashSlot::updateOrCreate(
            [
                'outlet_id' => $outlet->id,
                'slot_date' => $scheduledAt->toDateString(),
                'slot_time' => '11:00:00',
            ],
            [
                'capacity' => 1,
                'booked_count' => 1,
                'status' => 'available',
            ]
        );

        $payload = [
            'vehicle_id' => $vehicle->id,
            'package_id' => $package->id,
            'service_type' => 'outlet',
            'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
            'service_address' => $outlet->address,
            'outlet_id' => $outlet->id,
        ];

        // Second booking attempt to the same slot should fail
        $response = $this->actingAs($customer, 'sanctum')
            ->postJson('/api/bookings', $payload);

        $response->assertStatus(422);
        $this->assertStringContainsString('penuh', $response->json('message'));
    }

    public function test_booking_slot_count_increments_on_create_and_decrements_on_complete()
    {
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $package = Package::first();
        $outlet = Outlet::first();
        $scheduledAt = now()->addDays(2);

        $payload = [
            'vehicle_id' => $vehicle->id,
            'package_id' => $package->id,
            'service_type' => 'outlet',
            'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
            'service_address' => $outlet->address,
            'outlet_id' => $outlet->id,
        ];

        // 1. Create booking
        $response = $this->actingAs($customer, 'sanctum')
            ->postJson('/api/bookings', $payload);

        $response->assertStatus(200);
        $bookingId = $response->json('booking.id');
        $booking = Booking::find($bookingId);
        $slot = \App\Models\WashSlot::find($booking->outlet_slot_id);

        $this->assertNotNull($slot);
        $this->assertEquals(1, $slot->fresh()->booked_count);

        // 2. Complete booking (via admin controller action)
        $admin = \App\Models\User::first();
        $booking->update(['status' => 'confirmed']); // complete requires confirmed first
        $this->actingAs($admin)
            ->patch("/bookings/{$booking->id}/complete");

        $this->assertEquals('completed', $booking->fresh()->status);
        $this->assertEquals(0, $slot->fresh()->booked_count);
    }
}
