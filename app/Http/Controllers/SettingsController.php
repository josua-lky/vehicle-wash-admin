<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:100',
            'contact_email' => 'required|email|max:150',
            'whatsapp' => 'required|string|max:20',
            'service_radius' => 'required|integer|min:1|max:100',
            'delivery_rate' => 'required|integer|min:0',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Profil bisnis berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function updateNotifications(Request $request)
    {
        $keys = ['notify_new_booking', 'notify_payment_received', 'notify_booking_cancelled', 'notify_bad_rating', 'notify_new_customer'];
        foreach ($keys as $key) {
            Setting::set($key, $request->has($key) ? '1' : '0');
        }

        return back()->with('success', 'Pengaturan notifikasi berhasil diperbarui.');
    }
}
