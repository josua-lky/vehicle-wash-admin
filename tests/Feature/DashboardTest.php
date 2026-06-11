<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_redirects_to_login_when_unauthenticated()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_loads_correctly_when_authenticated()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('monthlyData');
        $response->assertViewHas('weeklyData');
    }

    public function test_dashboard_calculates_correct_monthly_and_weekly_metrics()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        // Let's create some bookings for this year
        // We will mock/assign scheduled_at to specific months
        $thisMonth = now();
        $prevMonth = now()->subMonth();

        // Clear existing bookings for this month/year if needed to make test assertion clean,
        // but since we are using existing database and DatabaseTransactions, let's just create new ones
        // and check that the counts reflect the increments.
        $initialResponse = $this->actingAs($user)->get('/dashboard');
        $initialMonthly = $initialResponse->viewData('monthlyData');
        $initialWeekly = $initialResponse->viewData('weeklyData');

        // Create booking today (which is this month, this week, and today)
        $bookingToday = Booking::create([
            'booking_code' => 'VW-TEST-DSH1',
            'customer_id' => $customer->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 100000,
            'total_amount' => 100000,
        ]);

        // Create booking in previous month (if in the same year) or just check month calculations
        $monthIndex = now()->month - 1; // 0-indexed in PHP array (1-12 mapped to 0-11 in view monthlyData)
        
        $response = $this->actingAs($user)->get('/dashboard');
        $currentMonthly = $response->viewData('monthlyData');
        $currentWeekly = $response->viewData('weeklyData');
        $stats = $response->viewData('stats');

        $this->assertEquals($initialMonthly[$monthIndex] + 1, $currentMonthly[$monthIndex]);
        $this->assertEquals($initialResponse->viewData('stats')['bookings_today'] + 1, $stats['bookings_today']);
        $this->assertEquals($initialResponse->viewData('stats')['perf_completed'] + 1, $stats['perf_completed']);
    }
}
