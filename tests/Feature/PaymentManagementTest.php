<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_payment_management_page_requires_authentication()
    {
        $response = $this->get('/payments');
        $response->assertRedirect('/login');
    }

    public function test_payment_management_page_loads_with_required_view_variables()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/payments');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('revenueData');
        $response->assertViewHas('payouts');
        $response->assertViewHas('methodBreakdown');

        $revenueData = $response->viewData('revenueData');
        $payouts = $response->viewData('payouts');
        $methodBreakdown = $response->viewData('methodBreakdown');

        $this->assertCount(12, $revenueData);
        $this->assertIsArray($payouts);
        $this->assertIsArray($methodBreakdown);
    }

    public function test_processing_payouts_returns_success_flash_message()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $tech = \App\Models\Technician::first() ?? \App\Models\Technician::create([
            'name' => 'Budi Teknisi Test',
            'email' => 'budi.test.tech@example.com',
            'phone' => '081234567891',
            'status' => 'active',
            'join_date' => now(),
        ]);
        $cust = Customer::first() ?? Customer::factory()->create();

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-PAYOUT-1',
            'customer_id' => $cust->id,
            'technician_id' => $tech->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'service_type' => 'home',
            'salary_paid' => false,
        ]);

        $response = $this->actingAs($user)
            ->post('/payments/process-payouts');

        $response->assertStatus(302);
        $response->assertRedirect('/payments/payout-slip');
        $response->assertSessionHas('payout_slip_data');
        $this->assertTrue((bool)$booking->fresh()->salary_paid);
    }

    public function test_admin_can_view_payment_details()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-DET-B',
            'customer_id' => Customer::first()->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 50000,
            'total_amount' => 50000,
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'ewallet',
            'amount' => 50000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)
            ->get("/payments/{$payment->id}");

        $response->assertStatus(200);
        $response->assertSee('PAY-' . $payment->id);
    }

    public function test_payment_actions_confirm_and_refund_using_patch()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $booking1 = Booking::create([
            'booking_code' => 'VW-TEST-REF-B1',
            'customer_id' => Customer::first()->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 75000,
            'total_amount' => 75000,
        ]);

        $payment = Payment::create([
            'booking_id' => $booking1->id,
            'payment_method' => 'ewallet',
            'amount' => 75000,
            'status' => 'paid',
        ]);

        // Test refund PATCH
        $responseRefund = $this->actingAs($user)
            ->patch("/payments/{$payment->id}/refund");
        $responseRefund->assertStatus(302);
        $responseRefund->assertSessionHas('success', 'Refund berhasil diproses dan saldo pelanggan telah dikembalikan.');
        $this->assertEquals('refunded', $payment->fresh()->status);

        // Test confirm PATCH
        $booking2 = Booking::create([
            'booking_code' => 'VW-TEST-REF-B2',
            'customer_id' => Customer::first()->id,
            'scheduled_at' => now(),
            'status' => 'pending',
            'subtotal' => 75000,
            'total_amount' => 75000,
        ]);

        $paymentPending = Payment::create([
            'booking_id' => $booking2->id,
            'payment_method' => 'ewallet',
            'amount' => 75000,
            'status' => 'pending',
        ]);

        $responseConfirm = $this->actingAs($user)
            ->patch("/payments/{$paymentPending->id}/confirm");
        $responseConfirm->assertStatus(302);
        $responseConfirm->assertSessionHas('success', 'Pembayaran berhasil dikonfirmasi.');
        $this->assertEquals('paid', $paymentPending->fresh()->status);
    }

    public function test_payment_filters_work()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        // Access page with status filter
        $response = $this->actingAs($user)
            ->get('/payments?status=paid');
        $response->assertStatus(200);

        // Access page with search query
        $responseSearch = $this->actingAs($user)
            ->get('/payments?search=Budi');
        $responseSearch->assertStatus(200);
    }

    public function test_cancellation_sets_refund_requested_and_notifies_admin()
    {
        $customer = Customer::first() ?: Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '08122334455',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TCR',
            'customer_id' => $customer->id,
            'scheduled_at' => now(),
            'status' => 'confirmed',
            'subtotal' => 50000,
            'total_amount' => 50000,
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'ewallet',
            'amount' => 50000,
            'status' => 'paid',
        ]);

        $initialNotifCount = \App\Models\PushNotification::where('type', 'refund_requested')->count();

        $response = $this->actingAs($customer, 'sanctum')
            ->putJson("/api/bookings/{$booking->id}/cancel", ['reason' => 'Batal dong']);

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertTrue((bool)$payment->fresh()->refund_requested);

        // Check notification
        $newNotifCount = \App\Models\PushNotification::where('type', 'refund_requested')->count();
        $this->assertEquals($initialNotifCount + 1, $newNotifCount);
    }

    public function test_payment_index_payouts_works_when_technician_is_deleted()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $connection = \Illuminate\Support\Facades\DB::connection();
        if ($connection->getDriverName() === 'sqlite') {
            $connection->statement('PRAGMA foreign_keys = OFF;');
        } else {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $booking = Booking::create([
            'booking_code' => 'VW-DEL-TECH-B',
            'customer_id' => Customer::first()->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'technician_id' => 99999, // Non-existent/deleted technician
        ]);

        if ($connection->getDriverName() === 'sqlite') {
            $connection->statement('PRAGMA foreign_keys = ON;');
        } else {
            $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $response = $this->actingAs($user)
            ->get('/payments');

        $response->assertStatus(200);
        $payouts = $response->viewData('payouts');
        $this->assertIsArray($payouts);
    }

    public function test_payment_export_works_when_booking_is_deleted()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $connection = \Illuminate\Support\Facades\DB::connection();
        if ($connection->getDriverName() === 'sqlite') {
            $connection->statement('PRAGMA foreign_keys = OFF;');
        } else {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $payment = Payment::create([
            'booking_id' => 99999, // Non-existent booking
            'payment_method' => 'ewallet',
            'amount' => 50000,
            'status' => 'paid',
        ]);

        if ($connection->getDriverName() === 'sqlite') {
            $connection->statement('PRAGMA foreign_keys = ON;');
        } else {
            $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Test export CSV
        $responseExport = $this->actingAs($user)
            ->get('/payments/export?format=excel');
        $responseExport->assertStatus(200);

        // Test print PDF
        $responsePrint = $this->actingAs($user)
            ->get('/payments/export?format=pdf');
        $responsePrint->assertStatus(200);
    }
}
