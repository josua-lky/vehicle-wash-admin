<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::where('email', 'budi.s@example.com')
            ->orWhere('phone', '08123456789')
            ->first();

        if (!$this->customer) {
            $this->customer = Customer::create([
                'name' => 'Budi Santoso',
                'email' => 'budi.s@example.com',
                'phone' => '08123456789',
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);
        } else {
            $this->customer->update(['status' => 'active']);
        }

        Vehicle::where('customer_id', $this->customer->id)->delete();
    }

    public function test_customer_can_store_vehicle()
    {
        $response = $this->actingAs($this->customer, 'sanctum')
            ->postJson('/api/vehicles', [
                'brand' => 'Honda',
                'model' => 'Beat',
                'license_plate' => 'B 1234 ABC',
                'type' => 'roda_2',
                'color' => 'Hitam',
                'year' => '2021',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'vehicle' => ['id', 'brand', 'model', 'license_plate', 'type']
            ]);

        $this->assertDatabaseHas('vehicles', [
            'customer_id' => $this->customer->id,
            'brand' => 'Honda',
            'model' => 'Beat',
            'license_plate' => 'B 1234 ABC',
            'type' => 'roda_2',
        ]);
    }

    public function test_customer_can_list_vehicles()
    {
        Vehicle::create([
            'customer_id' => $this->customer->id,
            'type' => 'roda_4',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'license_plate' => 'B 5678 DEF',
        ]);

        $response = $this->actingAs($this->customer, 'sanctum')
            ->getJson('/api/vehicles');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'brand' => 'Toyota',
                'model' => 'Avanza',
            ]);
    }

    public function test_customer_cannot_store_vehicle_with_invalid_year()
    {
        $response = $this->actingAs($this->customer, 'sanctum')
            ->postJson('/api/vehicles', [
                'brand' => 'Honda',
                'model' => 'Beat',
                'license_plate' => 'B 1234 ABC',
                'type' => 'roda_2',
                'year' => 124, // Invalid year (out of range)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['year']);
    }

    public function test_customer_can_store_vehicle_with_null_or_empty_year()
    {
        // Null year
        $response1 = $this->actingAs($this->customer, 'sanctum')
            ->postJson('/api/vehicles', [
                'brand' => 'Honda',
                'model' => 'Beat',
                'license_plate' => 'B 1234 ABC',
                'type' => 'roda_2',
                'year' => null,
            ]);

        $response1->assertStatus(200);

        // Empty string year (converted to null by middleware)
        $response2 = $this->actingAs($this->customer, 'sanctum')
            ->postJson('/api/vehicles', [
                'brand' => 'Honda',
                'model' => 'Beat',
                'license_plate' => 'B 1234 ABC',
                'type' => 'roda_2',
                'year' => '',
            ]);

        $response2->assertStatus(200);
    }

    public function test_customer_cannot_store_vehicle_with_invalid_type()
    {
        $response = $this->actingAs($this->customer, 'sanctum')
            ->postJson('/api/vehicles', [
                'brand' => 'Honda',
                'model' => 'Beat',
                'license_plate' => 'B 1234 ABC',
                'type' => 'mobil', // Invalid type (must be roda_2 or roda_4)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}
