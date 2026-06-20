<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Normalize phone number
        $phone = $request->phone;
        $phone = preg_replace('/[^0-9+]/', '', $phone); // remove spaces, dashes
        if (str_starts_with($phone, '+62')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        } elseif (!str_starts_with($phone, '0') && !empty($phone)) {
            $phone = '0' . $phone;
        }
        $request->merge(['phone' => $phone]);

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required|min:6',
            'phone' => 'required',
            'address' => 'nullable|string'
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'],
            'status' => 'active',
        ]);

        \App\Models\PushNotification::notifyAdmin(
            'new_customer',
            'Pelanggan Baru',
            "Pelanggan baru {$customer->name} ({$customer->phone}) telah mendaftar.",
            ['customer_id' => $customer->id]
        );

        if (!empty($validated['address'])) {
            \App\Models\UserAddress::create([
                'customer_id' => $customer->id,
                'label' => 'Utama',
                'address' => $validated['address'],
                'is_default' => true
            ]);
        }

        // Register on OnoPay
        try {
            $client = new \GuzzleHttp\Client(['cookies' => true]);
            $response = $client->get('https://onopay.web.id/user/register');
            $html = (string) $response->getBody();
            
            preg_match('/name="_token"\s+value="([^"]+)"/', $html, $matches);
            $token = $matches[1] ?? null;
            
            if ($token) {
                $client->post('https://onopay.web.id/user/register', [
                    'form_params' => [
                        '_token' => $token,
                        'name' => $customer->name,
                        'email' => $customer->phone . '@onopay-temp.com',
                        'phone_number' => $customer->phone,
                        'password' => 'password123',
                        'password_confirmation' => 'password123',
                    ]
                ]);
            }
        } catch (\Exception $e) {
            \Log::info('OnoPay registration skipped/failed for ' . $customer->phone . ': ' . $e->getMessage());
        }


        $token = $customer->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $customer,
            'token' => $token
        ]);
    }

public function login(Request $request)
{
    $request->validate([

        'email' => 'required|email',

        'password' => 'required'

    ]);

    $customer = Customer::where(
        'email',
        $request->email
    )->first();

    if (
        !$customer ||
        !Hash::check(
            $request->password,
            $customer->password
        )
    ) {

        return response()->json([

            'message' =>
                'Invalid credentials'

        ], 401);
    }

    if ($customer->status !== 'active') {
        return response()->json([
            'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.'
        ], 403);
    }

    $token = $customer
        ->createToken('mobile')
        ->plainTextToken;

    return response()->json([

        'user' => $customer,

        'token' => $token

    ]);
}

    public function profile(Request $request)
    {
        $user = $request->user();
        if ($user instanceof \App\Models\Technician) {
            $user->load('outlet');
        }
        return response()->json([
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logout success'
        ]);
    }

    public function updateProfile(
        Request $request
    )
    {
        $customer = $request->user();
    
        $validated = $request->validate([
            'name' => 'required',
            'profile_photo' => 'nullable'
        ]);

        $photo = $request->input('profile_photo');
        $profilePhotoPath = $customer->profile_photo;

        if ($photo && preg_match('/^data:image\/(\w+);base64,/', $photo, $type)) {
            $data = substr($photo, strpos($photo, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                $data = str_replace(' ', '+', $data);
                $data = base64_decode($data);

                if ($data !== false) {
                    $fileName = 'avatars/' . uniqid() . '.' . $type;
                    \Storage::disk('public')->put($fileName, $data);
                    $profilePhotoPath = $fileName;
                }
            }
        } elseif ($photo === null) {
            $profilePhotoPath = null;
        } elseif (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            $profilePhotoPath = $photo;
        }

        $customer->update([
            'name' => $validated['name'],
            'profile_photo' => $profilePhotoPath
        ]);
    
        return response()->json([
            'user' => $customer,
            'message' => 'Profile updated'
        ]);
    }

    public function getAddress(Request $request)
    {
        $customer = $request->user();
        $address = \App\Models\UserAddress::where('customer_id', $customer->id)
            ->where('is_default', true)
            ->first();
            
        return response()->json([
            'success' => true,
            'address' => $address
        ]);
    }

    public function updateAddress(Request $request)
    {
        $customer = $request->user();
        $validated = $request->validate([
            'address' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        \App\Models\UserAddress::where('customer_id', $customer->id)
            ->update(['is_default' => false]);

        $address = \App\Models\UserAddress::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'label' => 'Utama'
            ],
            [
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'is_default' => true
            ]
        );

        return response()->json([
            'success' => true,
            'address' => $address,
            'message' => 'Alamat berhasil diperbarui'
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $customer = $request->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json([
                'message' => 'Kata sandi saat ini tidak cocok.'
            ], 422);
        }

        $customer->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Kata sandi berhasil diperbarui'
        ]);
    }

    public function registerOnoPay(Request $request)
    {
        $customer = $request->user();
        $phone = $request->input('phone');
        
        if (empty($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon wajib diisi.'
            ], 400);
        }

        // Normalize phone number
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '+62')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        } elseif (!str_starts_with($phone, '0') && !empty($phone)) {
            $phone = '0' . $phone;
        }

        $name = $customer->name;
        $email = $phone . '@onopay-temp.com';

        // Register on OnoPay
        try {
            $client = new \GuzzleHttp\Client(['cookies' => true]);
            $response = $client->get('https://onopay.web.id/user/register');
            $html = (string) $response->getBody();
            
            preg_match('/name="_token"\s+value="([^"]+)"/', $html, $matches);
            $token = $matches[1] ?? null;
            
            if ($token) {
                $client->post('https://onopay.web.id/user/register', [
                    'form_params' => [
                        '_token' => $token,
                        'name' => $name,
                        'email' => $email,
                        'phone_number' => $phone,
                        'password' => 'password123',
                        'password_confirmation' => 'password123',
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OnoPay registration failed: ' . $e->getMessage()
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'OnoPay user registered successfully.'
        ]);
    }
}
