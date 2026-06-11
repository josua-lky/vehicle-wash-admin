<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Review;
use App\Models\PushNotification;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_notification_settings_can_be_saved()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($user)
            ->put('/settings/notifications', [
                'notify_new_booking' => '1',
                'notify_payment_received' => '1',
                'notify_new_customer' => '1',
                // notify_booking_cancelled and notify_bad_rating are omitted (unchecked)
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals('1', Setting::get('notify_new_booking'));
        $this->assertEquals('1', Setting::get('notify_payment_received'));
        $this->assertEquals('1', Setting::get('notify_new_customer'));
        $this->assertEquals('0', Setting::get('notify_booking_cancelled'));
        $this->assertEquals('0', Setting::get('notify_bad_rating'));
    }

    public function test_new_booking_notification_triggers_based_on_settings()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);
        $customer = Customer::first() ?? Customer::factory()->create();

        // 1. Enable new booking notifications
        Setting::set('notify_new_booking', '1');
        
        $initialCount = PushNotification::count();

        // Create booking via web controller store (we simulate the request)
        $this->actingAs($user)->post('/bookings', [
            'customer_id' => $customer->id,
            'package_id' => 1,
            'service_type' => 'outlet',
            'scheduled_at' => now()->addDays(2)->toDateTimeString(),
            'vehicle_name' => 'Toyota Yaris',
            'vehicle_type' => 'roda_4',
        ]);

        $this->assertEquals($initialCount + 1, PushNotification::count());
        $latestNotification = PushNotification::latest()->first();
        $this->assertEquals('new_booking', $latestNotification->type);

        // 2. Disable new booking notifications
        Setting::set('notify_new_booking', '0');

        $this->actingAs($user)->post('/bookings', [
            'customer_id' => $customer->id,
            'package_id' => 1,
            'service_type' => 'outlet',
            'scheduled_at' => now()->addDays(2)->toDateTimeString(),
            'vehicle_name' => 'Toyota Prius',
            'vehicle_type' => 'roda_4',
        ]);

        // Count should not increase because it is disabled
        $this->assertEquals($initialCount + 1, PushNotification::count());
    }

    public function test_api_unread_notifications_returns_json()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);
        
        PushNotification::create([
            'title' => 'Test Notification',
            'body' => 'Test Body',
            'type' => 'general',
            'is_read' => false
        ]);

        $response = $this->actingAs($user)
            ->get('/notifications/unread');

        $response->assertStatus(200);
        $response->assertJsonStructure(['count', 'notifications']);
        $this->assertGreaterThan(0, $response->json('count'));
    }

    public function test_marking_notification_as_read()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);
        
        $notif = PushNotification::create([
            'title' => 'Test Notification',
            'body' => 'Test Body',
            'type' => 'general',
            'is_read' => false
        ]);

        $response = $this->actingAs($user)
            ->patch("/notifications/{$notif->id}/read");

        $response->assertStatus(200);
        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_new_customer_registration_triggers_notification()
    {
        // 1. Enable new customer notification
        Setting::set('notify_new_customer', '1');

        $initialCount = PushNotification::count();

        // Register customer via API AuthController register endpoint
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe Registration Test',
            'email' => 'johndoe_test_reg@example.com',
            'phone' => '081299991111',
            'password' => 'password123',
            'address' => 'Jakarta Barat'
        ]);

        $response->assertStatus(200);

        // Verify notification is created
        $this->assertEquals($initialCount + 1, PushNotification::count());
        $latestNotification = PushNotification::latest()->first();
        $this->assertEquals('new_customer', $latestNotification->type);
        $this->assertStringContainsString('John Doe Registration Test', $latestNotification->body);

        // 2. Disable new customer notification
        Setting::set('notify_new_customer', '0');

        $this->postJson('/api/register', [
            'name' => 'Jane Doe Registration Test',
            'email' => 'janedoe_test_reg@example.com',
            'phone' => '081299992222',
            'password' => 'password123',
            'address' => 'Jakarta Selatan'
        ]);

        // Count should not increase
        $this->assertEquals($initialCount + 1, PushNotification::count());
    }
}
