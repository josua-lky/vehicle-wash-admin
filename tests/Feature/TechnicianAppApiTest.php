<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Package;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicianAppApiTest extends TestCase
{
    use DatabaseTransactions;

    protected $technician;
    protected $outlet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outlet = Outlet::first() ?? Outlet::create([
            'name' => 'Outlet Test',
            'address' => 'Jl. Test No. 123',
            'phone' => '08123456789',
            'status' => 'active',
        ]);

        $this->technician = Technician::create([
            'name' => 'Budi Teknisi',
            'email' => 'budi.teknisi@example.com',
            'password' => Hash::make('secret123'),
            'phone' => '08123456780',
            'specialization' => 'Cuci Motor & Mobil',
            'area' => 'Jakarta',
            'outlet_id' => $this->outlet->id,
            'status' => 'active',
            'rating' => 5.0,
            'total_orders' => 0,
            'join_date' => now(),
        ]);
    }

    public function test_technician_can_login()
    {
        $response = $this->postJson('/api/technician/login', [
            'email' => 'budi.teknisi@example.com',
            'password' => 'secret123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
                'role'
            ])
            ->assertJson([
                'role' => 'technician'
            ]);
    }

    public function test_technician_invalid_credentials_cannot_login()
    {
        $response = $this->postJson('/api/technician/login', [
            'email' => 'budi.teknisi@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Email atau kata sandi salah.'
            ]);
    }

    public function test_technician_can_fetch_assigned_bookings()
    {
        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T1',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'confirmed',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
            'service_address' => 'Alamat Pelanggan 123',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $response = $this->actingAs($this->technician, 'sanctum')
            ->getJson('/api/technician/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $booking->id,
                'service_address' => 'Alamat Pelanggan 123'
            ]);
    }

    public function test_technician_can_update_booking_status_to_on_way()
    {
        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T2',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'confirmed',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->technician, 'sanctum')
            ->post("/api/technician/bookings/{$booking->id}/status", [
                'status' => 'on_way'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'on_way');

        $this->assertEquals('on_way', $booking->fresh()->status);
    }

    public function test_technician_can_update_booking_status_to_in_progress_with_before_photo()
    {
        Storage::fake('public');

        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T3',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'on_way',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('before.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($this->technician, 'sanctum')
            ->call('POST', "/api/technician/bookings/{$booking->id}/status", [
                'status' => 'in_progress',
            ], [], [
                'before_photo' => $file
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'in_progress');

        $booking = $booking->fresh();
        $this->assertEquals('in_progress', $booking->status);
        $this->assertNotNull($booking->before_photo);
        Storage::disk('public')->assertExists($booking->before_photo);
    }

    public function test_technician_can_update_booking_status_to_completed_with_after_photo()
    {
        Storage::fake('public');

        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T4',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'in_progress',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('after.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($this->technician, 'sanctum')
            ->call('POST', "/api/technician/bookings/{$booking->id}/status", [
                'status' => 'completed',
            ], [], [
                'after_photo' => $file
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'completed');

        $booking = $booking->fresh();
        $this->assertEquals('completed', $booking->status);
        $this->assertNotNull($booking->after_photo);
        Storage::disk('public')->assertExists($booking->after_photo);
    }

    public function test_technician_can_update_location()
    {
        $response = $this->actingAs($this->technician, 'sanctum')
            ->postJson('/api/technician/location', [
                'latitude' => -6.200000,
                'longitude' => 106.816666
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->technician = $this->technician->fresh();
        $this->assertEquals(-6.200000, $this->technician->latitude);
        $this->assertEquals(106.816666, $this->technician->longitude);
    }

    public function test_technician_can_send_and_get_chat_messages()
    {
        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T5',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'confirmed',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
        ]);

        // 1. Send chat message
        $response = $this->actingAs($this->technician, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/chat", [
                'sender_type' => 'technician',
                'message' => 'Halo, saya sedang menuju lokasi Anda.'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Fetch messages
        $response = $this->actingAs($this->technician, 'sanctum')
            ->getJson("/api/bookings/{$booking->id}/chat");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'sender_type' => 'technician',
                'message' => 'Halo, saya sedang menuju lokasi Anda.'
            ]);
    }

    public function test_customer_can_send_and_get_chat_messages()
    {
        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T7',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'confirmed',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
        ]);

        // 1. Send chat message as customer
        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/chat", [
                'sender_type' => 'customer',
                'message' => 'Halo pak, tolong dikabari ya kalau sudah dekat.'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Fetch messages as customer
        $response = $this->actingAs($customer, 'sanctum')
            ->getJson("/api/bookings/{$booking->id}/chat");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'sender_type' => 'customer',
                'message' => 'Halo pak, tolong dikabari ya kalau sudah dekat.'
            ]);
    }


    public function test_technician_rating_updates_and_review_is_returned()
    {
        $customer = Customer::first() ?? Customer::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'phone' => '08111111111',
            'password' => Hash::make('password123'),
        ]);

        $package = Package::first() ?? Package::create([
            'name' => 'Premium Wash',
            'description' => 'Premium washing package',
            'price' => 50000,
            'duration' => 60,
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-T6',
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'technician_id' => $this->technician->id,
            'outlet_id' => $this->outlet->id,
            'service_type' => 'home',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'completed',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'paid',
        ]);

        // Submit review via API
        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/review", [
                'rating' => 5,
                'comment' => 'Sangat memuaskan!'
            ]);

        $response->assertStatus(200);

        // Fetch bookings for technician
        $response = $this->actingAs($this->technician, 'sanctum')
            ->getJson('/api/technician/bookings');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'rating' => 5,
                'comment' => 'Sangat memuaskan!'
            ]);

        // Verify technician profile rating got updated in database
        $this->assertEquals(5.0, $this->technician->fresh()->rating);
        $this->assertEquals(1, $this->technician->fresh()->total_orders);
    }
}
