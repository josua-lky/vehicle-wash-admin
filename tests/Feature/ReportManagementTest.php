<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_report_page_requires_authentication()
    {
        $response = $this->get('/reports');
        $response->assertRedirect('/login');
    }

    public function test_report_page_loads_with_required_view_variables()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/reports');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('outlets');
        $response->assertViewHas('from');
        $response->assertViewHas('to');
        $response->assertViewHas('months');
        $response->assertViewHas('revenueData');
        $response->assertViewHas('volumeData');
        $response->assertViewHas('targetRevenue');
        $response->assertViewHas('targetVolume');
        $response->assertViewHas('serviceDistribution');
        $response->assertViewHas('outletPerformance');
        $response->assertViewHas('leaderboard');
        $response->assertViewHas('recentPayments');

        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('monthly_revenue', $stats);
        $this->assertArrayHasKey('orders_served', $stats);
        $this->assertArrayHasKey('avg_per_order', $stats);
        $this->assertArrayHasKey('satisfaction', $stats);

        $this->assertCount(12, $response->viewData('months'));
        $this->assertCount(12, $response->viewData('revenueData'));
        $this->assertCount(12, $response->viewData('volumeData'));
    }

    public function test_report_page_filters_work()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        // Create an outlet
        $outlet = Outlet::create([
            'name' => 'Outlet Test Feature',
            'address' => 'Jl. Test No. 123',
            'phone' => '081234567890',
            'status' => 'active',
            'open_time' => '08:00:00',
            'close_time' => '20:00:00',
            'capacity_per_hour' => 4,
        ]);

        $response = $this->actingAs($user)
            ->get("/reports?outlet_id={$outlet->id}&date_from=" . now()->startOfMonth()->toDateString() . "&date_to=" . now()->toDateString());

        $response->assertStatus(200);
        
        $outlets = $response->viewData('outlets');
        $this->assertTrue($outlets->contains('id', $outlet->id));
    }
}
