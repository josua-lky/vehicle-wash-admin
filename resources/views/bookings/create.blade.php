@extends('layouts.app')
@section('title', 'Tambah Booking Baru')

@section('content')
<div class="p-6 space-y-6" x-data="{ serviceType: '{{ old('service_type', 'home') }}' }">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/bookings" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Booking
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 max-w-2xl">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Tambah Booking Baru</h3>
            <p class="text-xs text-slate-400 mt-0.5">Buat reservasi pencucian baru untuk pelanggan.</p>
        </div>
        
        <form method="POST" action="/bookings" class="p-6 space-y-4">
            @csrf

            @if ($errors->any())
            <div class="p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl">
                <div class="font-semibold mb-1">Terjadi kesalahan input:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Customer ID --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pelanggan <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="">Pilih Pelanggan</option>
                        @foreach($customers ?? [] as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Vehicle Name --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Kendaraan <span class="text-red-500">*</span></label>
                    <input type="text" name="vehicle_name" value="{{ old('vehicle_name') }}" required placeholder="Contoh: Toyota Avanza, Honda Vario"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>

                {{-- Vehicle Type --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Spesialisasi Kendaraan <span class="text-red-500">*</span></label>
                    <select name="vehicle_type" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="roda_2" {{ old('vehicle_type') === 'roda_2' ? 'selected' : '' }}>Motor (Roda 2)</option>
                        <option value="roda_4" {{ old('vehicle_type') === 'roda_4' ? 'selected' : '' }}>Mobil (Roda 4)</option>
                    </select>
                </div>

                {{-- Package ID --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Paket Cuci <span class="text-red-500">*</span></label>
                    <select name="package_id" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="">Pilih Paket Cuci</option>
                        @foreach($packages ?? [] as $p)
                        <option value="{{ $p->id }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} - {{ $p->vehicle_type === 'roda_2' ? 'Motor' : 'Mobil' }} (Rp {{ number_format($p->price, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Service Type --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Tipe Layanan <span class="text-red-500">*</span></label>
                    <select name="service_type" x-model="serviceType" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="home">Home Service (Ke Rumah)</option>
                        <option value="outlet">Outlet Service (Ke Outlet)</option>
                    </select>
                </div>

                {{-- Scheduled At --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Jadwal Pencucian <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required min="{{ date('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>

                {{-- Outlet ID --}}
                <div class="md:col-span-2" x-show="serviceType === 'outlet'" x-cloak>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pilih Outlet <span class="text-red-500">*</span></label>
                    <select name="outlet_id" ::required="serviceType === 'outlet'" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="">Pilih Outlet</option>
                        @foreach($outlets ?? [] as $o)
                        <option value="{{ $o->id }}" {{ old('outlet_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Service Address --}}
                <div class="md:col-span-2" x-show="serviceType === 'home'" x-cloak>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Alamat Pelayanan <span class="text-red-500">*</span></label>
                    <textarea name="service_address" ::required="serviceType === 'home'" rows="3" placeholder="Alamat lengkap lokasi penjemputan/pencucian..."
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none">{{ old('service_address') }}</textarea>
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Catatan khusus dari pelanggan..."
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/bookings" class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-xl text-slate-900 shadow" style="background:#F0C419;">
                    Buat Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
