<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_settings_page_requires_authentication()
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }

    public function test_settings_page_loads_successfully_without_payment_config()
    {
        $user = User::first() ?? User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/settings');

        $response->assertStatus(200);
        $response->assertDontSee('Midtrans');
        $response->assertDontSee('Xendit');
        $response->assertDontSee('Konfigurasi Payment Gateway');
    }
}
