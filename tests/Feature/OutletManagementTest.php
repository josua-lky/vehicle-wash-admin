<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\WashSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OutletManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_outlet_index_page_requires_authentication()
    {
        $response = $this->get('/outlets');
        $response->assertRedirect('/login');
    }

    public function test_outlet_index_page_loads_with_required_view_variables()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $today = today()->toDateString();

        // Measure initial state due to pre-existing seed data
        $initialAvailableSlots = Outlet::where('status', 'active')
            ->get()
            ->sum(function ($outlet) {
                if (!$outlet->open_time || !$outlet->close_time) {
                    return 0;
                }
                $open = \Carbon\Carbon::parse($outlet->open_time);
                $close = \Carbon\Carbon::parse($outlet->close_time);
                $hours = $close->diffInHours($open);
                return $hours * $outlet->capacity_per_hour;
            });

        $initialBooked = WashSlot::whereHas('outlet', function ($q) {
            $q->where('status', 'active');
        })->sum('booked_count');

        $initialCapacity = $initialAvailableSlots;

        // Create active outlet
        $outlet = Outlet::create([
            'name' => 'Outlet Depok Test',
            'address' => 'Jl. Margonda Raya No. 12',
            'phone' => '021-99998888',
            'status' => 'active',
            'open_time' => '07:00:00',
            'close_time' => '21:00:00',
            'capacity_per_hour' => 4,
        ]);

        // Create wash slots today
        WashSlot::create([
            'outlet_id' => $outlet->id,
            'slot_date' => $today,
            'slot_time' => '08:00:00',
            'capacity' => 4,
            'booked_count' => 1,
            'status' => 'available',
        ]);

        WashSlot::create([
            'outlet_id' => $outlet->id,
            'slot_date' => $today,
            'slot_time' => '09:00:00',
            'capacity' => 4,
            'booked_count' => 4, // Full
            'status' => 'available',
        ]);

        $response = $this->actingAs($user)
            ->get('/outlets');

        $response->assertStatus(200);
        $response->assertViewHas('outlets');
        $response->assertViewHas('stats');

        $stats = $response->viewData('stats');
        $this->assertEquals($outlet->id, $response->viewData('outlets')->firstWhere('id', $outlet->id)->id);
        
        // Expected available slots: initial + (14 * 4) = initial + 56
        $this->assertEquals($initialAvailableSlots + 56, $stats['available_slots']);
        
        // Expected utilization calculation:
        $newBooked = $initialBooked + 5;
        $newCapacity = $initialCapacity + 56;
        $expectedUtilization = ($newCapacity > 0 ? round(($newBooked / $newCapacity) * 100) : 0) . '%';
        $this->assertEquals($expectedUtilization, $stats['utilization']);
    }
}
