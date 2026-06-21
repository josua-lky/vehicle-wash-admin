@extends('layouts.app')
@section('title', 'Edit Data Teknisi - ' . $technician->name)

@section('content')
<script>
function confirmTechnicianStatusChange(form) {
    const currentStatus = '{{ $technician->status }}';
    const newStatus = form.status.value;
    if (newStatus === 'inactive' && currentStatus !== 'inactive') {
        return confirm('Apakah Anda yakin ingin menonaktifkan teknisi ini? Teknisi yang dinonaktifkan tidak akan dapat menggunakan aplikasi.');
    }
    return true;
}
</script>
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/technicians" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen Teknisi
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 max-w-2xl">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Data Teknisi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah informasi profil dan area tugas teknisi.</p>
        </div>
        <form method="POST" action="/technicians/{{ $technician->id }}" class="p-6 space-y-4" onsubmit="return confirmTechnicianStatusChange(this);">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $technician->name) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nomor HP *</label>
                    <input type="text" name="phone" value="{{ old('phone', $technician->phone) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $technician->email) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="******" minlength="6"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Spesialisasi *</label>
                    <select name="specialization" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="motor" {{ old('specialization', $technician->specialization) === 'motor' ? 'selected' : '' }}>Motor</option>
                        <option value="mobil" {{ old('specialization', $technician->specialization) === 'mobil' ? 'selected' : '' }}>Mobil</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Area Kerja</label>
                    <input type="text" name="area" value="{{ old('area', $technician->area) }}"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Outlet</label>
                    <select name="outlet_id" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <option value="">Tanpa outlet (freelance)</option>
                        @foreach($outlets ?? [] as $outlet)
                        <option value="{{ $outlet->id }}" {{ old('outlet_id', $technician->outlet_id) == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        @foreach(['active' => 'Aktif', 'inactive' => 'Nonaktif', 'cuti' => 'Cuti', 'busy' => 'Sibuk'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $technician->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/technicians" class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
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
