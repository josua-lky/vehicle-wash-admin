<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Promo;
use App\Models\PromoUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PromoManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_promo_management_page_requires_authentication()
    {
        $response = $this->get('/promos');
        $response->assertRedirect('/login');
    }

    public function test_promo_management_page_loads_with_required_view_variables()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/promos');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('active_promos', $stats);
        $this->assertArrayHasKey('total_usage', $stats);
        $this->assertArrayHasKey('total_discount', $stats);
        $this->assertArrayHasKey('avg_discount', $stats);
        $this->assertArrayHasKey('most_used_code', $stats);
    }

    public function test_promo_monthly_statistics_are_calculated_correctly()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $customer = Customer::first() ?? Customer::factory()->create();

        $promo = Promo::create([
            'name' => 'Test Promo Code',
            'code' => 'TPROMO99',
            'type' => 'percentage',
            'value' => 20,
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'booking_code' => 'VW-TEST-PRM-B',
            'customer_id' => $customer->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 100000,
            'total_amount' => 80000,
            'discount_amount' => 20000,
            'promo_id' => $promo->id,
        ]);

        // Create a PromoUsage
        PromoUsage::create([
            'promo_id' => $promo->id,
            'customer_id' => $customer->id,
            'booking_id' => $booking->id,
            'discount_applied' => 20000,
            'created_at' => now(),
        ]);

        // Create a second usage to guarantee it's the most used
        $booking2 = Booking::create([
            'booking_code' => 'VW-TEST-PRM-B2',
            'customer_id' => $customer->id,
            'scheduled_at' => now(),
            'status' => 'completed',
            'subtotal' => 100000,
            'total_amount' => 80000,
            'discount_amount' => 20000,
            'promo_id' => $promo->id,
        ]);

        PromoUsage::create([
            'promo_id' => $promo->id,
            'customer_id' => $customer->id,
            'booking_id' => $booking2->id,
            'discount_applied' => 20000,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/promos');
        $stats = $response->viewData('stats');

        $this->assertGreaterThanOrEqual(2, $stats['total_usage']);
        $this->assertGreaterThanOrEqual(40000, $stats['total_discount']);
        $this->assertEquals('TPROMO99', $stats['most_used_code']);
        $this->assertGreaterThanOrEqual(1, $stats['new_active_promos']);
    }
}
