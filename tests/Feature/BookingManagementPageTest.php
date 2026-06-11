<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BookingManagementPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_booking_management_page_requires_authentication()
    {
        $response = $this->get('/bookings');
        $response->assertRedirect('/login');
    }

    public function test_booking_management_page_loads_with_required_view_variables()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/bookings');

        $response->assertStatus(200);
        $response->assertViewHas('trendLabels');
        $response->assertViewHas('trendData');
        $response->assertViewHas('homeCount');
        $response->assertViewHas('outletCount');
        $response->assertViewHas('homePct');
        $response->assertViewHas('outletPct');

        $trendLabels = $response->viewData('trendLabels');
        $trendData = $response->viewData('trendData');
        
        $this->assertCount(7, $trendLabels);
        $this->assertCount(7, $trendData);
    }

    public function test_booking_management_page_displays_correct_dynamic_metrics()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        // Get initial values
        $initialResponse = $this->actingAs($user)->get('/bookings');
        $initialHomeCount = $initialResponse->viewData('homeCount');
        $initialOutletCount = $initialResponse->viewData('outletCount');
        $initialTrendData = $initialResponse->viewData('trendData');

        // Create a home service booking scheduled for today
        Booking::create([
            'booking_code' => 'VW-TEST-BMP1',
            'customer_id' => $customer->id,
            'service_type' => 'home',
            'scheduled_at' => now(),
            'status' => 'pending',
            'subtotal' => 50000,
            'total_amount' => 50000,
        ]);

        // Create an outlet booking scheduled for today
        Booking::create([
            'booking_code' => 'VW-TEST-BMP2',
            'customer_id' => $customer->id,
            'service_type' => 'outlet',
            'scheduled_at' => now(),
            'status' => 'pending',
            'subtotal' => 60000,
            'total_amount' => 60000,
        ]);

        $response = $this->actingAs($user)->get('/bookings');
        
        $newHomeCount = $response->viewData('homeCount');
        $newOutletCount = $response->viewData('outletCount');
        $newTrendData = $response->viewData('trendData');

        $this->assertEquals($initialHomeCount + 1, $newHomeCount);
        $this->assertEquals($initialOutletCount + 1, $newOutletCount);
        
        // Today is the last element of the trend data (index 6)
        $this->assertEquals($initialTrendData[6] + 2, $newTrendData[6]);
    }

    public function test_bookings_can_be_completed_successfully()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-COMP1',
            'customer_id' => $customer->id,
            'service_type' => 'outlet',
            'scheduled_at' => now(),
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->patch("/bookings/{$booking->id}/complete");
        
        $response->assertRedirect();
        $response->assertSessionHas('success', "Booking {$booking->booking_code} berhasil diselesaikan.");
        $this->assertEquals('completed', $booking->fresh()->status);
    }

    public function test_pending_bookings_cannot_be_completed()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-PEND-COMP',
            'customer_id' => $customer->id,
            'service_type' => 'outlet',
            'scheduled_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->patch("/bookings/{$booking->id}/complete");
        $response->assertRedirect('/bookings');
        $response->assertSessionHas('error', 'Pesanan harus dikonfirmasi terlebih dahulu sebelum diselesaikan.');
        $this->assertEquals('pending', $booking->fresh()->status);
    }

    public function test_completed_or_cancelled_bookings_cannot_be_modified()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        // 1. Completed booking
        $completedBooking = Booking::create([
            'booking_code' => 'VW-TEST-EDIT1',
            'customer_id' => $customer->id,
            'service_type' => 'outlet',
            'scheduled_at' => now(),
            'status' => 'completed',
        ]);

        // Try to complete
        $responseComplete1 = $this->actingAs($user)->patch("/bookings/{$completedBooking->id}/complete");
        $responseComplete1->assertRedirect('/bookings');
        $responseComplete1->assertSessionHas('error');

        // Try to confirm
        $responseConfirm1 = $this->actingAs($user)->patch("/bookings/{$completedBooking->id}/confirm");
        $responseConfirm1->assertRedirect('/bookings');
        $responseConfirm1->assertSessionHas('error');

        // Try to cancel
        $responseCancel1 = $this->actingAs($user)->patch("/bookings/{$completedBooking->id}/cancel", [
            'reason' => 'Testing restriction'
        ]);
        $responseCancel1->assertRedirect('/bookings');
        $responseCancel1->assertSessionHas('error');

        // Try to assign
        $responseAssign1 = $this->actingAs($user)->post("/bookings/{$completedBooking->id}/assign", [
            'technician_id' => 1
        ]);
        $responseAssign1->assertRedirect('/bookings');
        $responseAssign1->assertSessionHas('error');

        // 2. Cancelled booking
        $cancelledBooking = Booking::create([
            'booking_code' => 'VW-TEST-EDIT2',
            'customer_id' => $customer->id,
            'service_type' => 'outlet',
            'scheduled_at' => now(),
            'status' => 'cancelled',
        ]);

        // Try to complete
        $responseComplete2 = $this->actingAs($user)->patch("/bookings/{$cancelledBooking->id}/complete");
        $responseComplete2->assertRedirect('/bookings');
        $responseComplete2->assertSessionHas('error');

        // Try to confirm
        $responseConfirm2 = $this->actingAs($user)->patch("/bookings/{$cancelledBooking->id}/confirm");
        $responseConfirm2->assertRedirect('/bookings');
        $responseConfirm2->assertSessionHas('error');

        // Try to cancel
        $responseCancel2 = $this->actingAs($user)->patch("/bookings/{$cancelledBooking->id}/cancel", [
            'reason' => 'Testing restriction'
        ]);
        $responseCancel2->assertRedirect('/bookings');
        $responseCancel2->assertSessionHas('error');

        // Try to assign
        $responseAssign2 = $this->actingAs($user)->post("/bookings/{$cancelledBooking->id}/assign", [
            'technician_id' => 1
        ]);
        $responseAssign2->assertRedirect('/bookings');
        $responseAssign2->assertSessionHas('error');
    }

    public function test_cancelled_booking_detail_shows_cancellation_reason()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-REASON1',
            'customer_id' => $customer->id,
            'service_type' => 'outlet',
            'scheduled_at' => now(),
            'status' => 'cancelled',
            'cancelled_reason' => 'Customer requested refund'
        ]);

        $response = $this->actingAs($user)->get("/bookings/{$booking->id}");
        $response->assertStatus(200);
        $response->assertSee('Customer requested refund');
        $response->assertSee('Pesanan ini telah dibatalkan');
    }
}
