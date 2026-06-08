@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="p-6 space-y-5">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Pengaturan Sistem</h1>
        <p class="text-sm text-slate-500 mt-0.5">Konfigurasi umum aplikasi Vehicle Wash</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Profil Bisnis --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-5">Profil Bisnis</h3>
            <form method="POST" action="/settings/profile" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Bisnis</label>
                        <input type="text" name="app_name" value="Vehicle Wash" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Email Kontak</label>
                        <input type="email" name="contact_email" value="info@vehiclewash.id" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nomor WhatsApp CS</label>
                        <input type="text" name="whatsapp" value="08112345678" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Radius Layanan (km)</label>
                        <input type="number" name="service_radius" value="15" min="1" max="100" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Biaya Antar (Rp/km)</label>
                        <input type="number" name="delivery_rate" value="2000" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-xl text-slate-900" style="background:#F0C419;">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Akun Admin --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="font-semibold text-slate-800 mb-4 text-sm">Profil Admin</h3>
                <div class="text-center mb-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold text-white mx-auto mb-3" style="background:linear-gradient(135deg,#F0C419,#E67E22);">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <p class="font-semibold text-slate-800">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->email ?? '' }}</p>
                    <span class="badge badge-blue mt-1">{{ auth()->user()->role_label ?? 'Admin' }}</span>
                </div>
                <form method="POST" action="/settings/password" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Password Lama</label>
                        <input type="password" name="current_password" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Password Baru</label>
                        <input type="password" name="password" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50">
                    </div>
                    <button type="submit" class="w-full py-2.5 text-sm font-semibold rounded-xl text-white" style="background:#1B2337;">Ubah Password</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800 mb-3">Notifikasi</h3>
                @foreach(['Booking baru masuk','Pembayaran diterima','Booking dibatalkan','Rating buruk (< 3 bintang)'] as $notif)
                <label class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0 cursor-pointer">
                    <input type="checkbox" checked class="w-4 h-4 rounded" style="accent-color:#F0C419;">
                    <span class="text-xs text-slate-600">{{ $notif }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Payment Gateway Config --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100" id="payment">
        <h3 class="font-semibold text-slate-800 mb-5">Konfigurasi Payment Gateway</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="p-4 rounded-xl border-2 border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-sm">MT</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">Midtrans</p>
                        <span class="badge badge-green">Terhubung</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Server Key</label>
                        <input type="password" value="SB-Mid-server-xxxx" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Mode</label>
                        <select class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50">
                            <option>Sandbox (Testing)</option>
                            <option>Production</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-4 rounded-xl border-2 border-dashed border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                        <span class="text-green-600 font-bold text-sm">XE</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">Xendit</p>
                        <span class="badge badge-gray">Belum Terhubung</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mb-3">Hubungkan Xendit sebagai payment gateway cadangan.</p>
                <button class="w-full py-2 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Hubungkan Xendit</button>
            </div>
        </div>
    </div>
</div>
@endsection
