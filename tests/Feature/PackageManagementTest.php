<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Package;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PackageManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_package_index_page_requires_authentication()
    {
        $response = $this->get('/packages');
        $response->assertRedirect('/login');
    }

    public function test_package_index_page_loads_successfully()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);
        $package = Package::create([
            'name' => 'Ultra Polish Test',
            'description' => 'Super high gloss polish',
            'vehicle_type' => 'roda_4',
            'price' => 150000,
            'duration_minutes' => 60,
            'sort_order' => 10,
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->get('/packages');

        $response->assertStatus(200);
        $response->assertViewHas('packages');
        $response->assertViewHas('stats');
        $this->assertTrue($response->viewData('packages')->contains($package));
    }

    public function test_package_can_be_stored()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($user)->post('/packages', [
            'name' => 'New Service Package',
            'description' => 'Test package description',
            'vehicle_type' => 'all',
            'price' => 80000,
            'duration_minutes' => 45,
            'sort_order' => 5
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('packages', [
            'name' => 'New Service Package',
            'price' => 80000
        ]);
    }

    public function test_package_can_be_updated()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);
        $package = Package::create([
            'name' => 'Old Name',
            'description' => 'Old desc',
            'vehicle_type' => 'roda_2',
            'price' => 20000,
            'duration_minutes' => 25,
            'sort_order' => 1,
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->put("/packages/{$package->id}", [
            'name' => 'Updated Name',
            'description' => 'Updated desc',
            'vehicle_type' => 'roda_2',
            'price' => 25000,
            'duration_minutes' => 30,
            'sort_order' => 2,
            'is_active' => '0'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'Updated Name',
            'is_active' => false
        ]);
    }

    public function test_package_can_be_deleted()
    {
        $user = User::first() ?? User::factory()->create(['role' => 'super_admin']);
        $package = Package::create([
            'name' => 'Delete Me Package',
            'description' => 'To be deleted',
            'vehicle_type' => 'roda_4',
            'price' => 120000,
            'duration_minutes' => 90,
            'sort_order' => 99,
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->delete("/packages/{$package->id}");

        $response->assertRedirect('/packages');
        $this->assertDatabaseMissing('packages', [
            'id' => $package->id
        ]);
    }
}
