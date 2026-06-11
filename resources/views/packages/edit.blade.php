@extends('layouts.app')
@section('title', 'Edit Paket - ' . $package->name)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/packages" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Paket
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 max-w-2xl">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Data Paket Layanan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah nama, deskripsi, harga, durasi, status aktif, dan sort order paket.</p>
        </div>
        <form method="POST" action="/packages/{{ $package->id }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Paket *</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Deskripsi Layanan</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none">{{ old('description', $package->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Tipe Kendaraan *</label>
                        <select name="vehicle_type" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                            @foreach(['roda_2' => 'Roda 2 (Motor)', 'roda_4' => 'Roda 4 (Mobil)', 'all' => 'Semua Tipe'] as $val => $label)
                            <option value="{{ $val }}" {{ old('vehicle_type', $package->vehicle_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Harga Paket (Rp) *</label>
                        <input type="number" name="price" value="{{ old('price', (int)$package->price) }}" required min="0"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Durasi (Menit) *</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $package->duration_minutes) }}" required min="1"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Sort Order *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" required min="0"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Status Paket</label>
                    <select name="is_active" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="1" {{ old('is_active', $package->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('is_active', $package->is_active) ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/packages" class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-xl text-slate-900 shadow" style="background:#F0C419;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
