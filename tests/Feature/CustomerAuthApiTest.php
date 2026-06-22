<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_fails_with_unregistered_email()
    {
        // Ensure email doesn't exist
        Customer::where('email', 'unregistered@example.com')->delete();

        $response = $this->postJson('/api/login', [
            'email' => 'unregistered@example.com',
            'password' => 'anypassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'silahkan daftar terlebih dahulu'
            ]);
    }

    public function test_login_fails_with_incorrect_password()
    {
        $email = 'registered@example.com';
        
        Customer::where('email', $email)->delete();

        Customer::create([
            'name' => 'Registered User',
            'email' => $email,
            'phone' => '08999999999',
            'password' => Hash::make('correctpassword'),
            'status' => 'active'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'email/password salah'
            ]);
    }

    public function test_login_succeeds_with_correct_credentials()
    {
        $email = 'registered@example.com';
        
        Customer::where('email', $email)->delete();

        Customer::create([
            'name' => 'Registered User',
            'email' => $email,
            'phone' => '08999999999',
            'password' => Hash::make('correctpassword'),
            'status' => 'active'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'correctpassword'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }
}
