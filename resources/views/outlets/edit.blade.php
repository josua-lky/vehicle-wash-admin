@extends('layouts.app')
@section('title', 'Edit Outlet - ' . $outlet->name)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/outlets/{{ $outlet->id }}" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Detail Outlet
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 max-w-2xl">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Data Outlet</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah informasi kontak, kapasitas, jam operasional, dan status outlet.</p>
        </div>
        <form method="POST" action="/outlets/{{ $outlet->id }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Outlet *</label>
                    <input type="text" name="name" value="{{ old('name', $outlet->name) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Alamat Lengkap *</label>
                    <textarea name="address" required rows="2"
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none">{{ old('address', $outlet->address) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $outlet->phone) }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Kapasitas / Jam *</label>
                        <input type="number" name="capacity_per_hour" value="{{ old('capacity_per_hour', $outlet->capacity_per_hour) }}" required min="1" max="50"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Jam Buka *</label>
                        <input type="time" name="open_time" value="{{ old('open_time', substr($outlet->open_time, 0, 5)) }}" required
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Jam Tutup *</label>
                        <input type="time" name="close_time" value="{{ old('close_time', substr($outlet->close_time, 0, 5)) }}" required
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Status Outlet</label>
                    <select name="status" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        @foreach(['active' => 'Aktif', 'inactive' => 'Nonaktif', 'maintenance' => 'Dalam Perbaikan (Maintenance)'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $outlet->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/outlets/{{ $outlet->id }}" class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
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
