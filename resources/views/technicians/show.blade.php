@extends('layouts.app')
@section('title', 'Profil Teknisi - ' . $technician->name)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/technicians" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen Teknisi
        </a>
        <a href="/technicians/{{ $technician->id }}/edit" class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#1B2337;">
            Edit Profil
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Summary Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center text-3xl font-bold text-white mb-4"
                     style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                    {{ strtoupper(substr($technician->name, 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-slate-800">{{ $technician->name }}</h2>
                <p class="text-sm text-slate-400 mt-0.5">{{ $technician->phone }}</p>
                <div class="mt-3 flex justify-center gap-2">
                    <span class="badge badge-blue">{{ $technician->specialization }}</span>
                    @php
                        $statusConfig = ['active'=>['Aktif','badge-green'],'inactive'=>['Nonaktif','badge-red'],'cuti'=>['Cuti','badge-yellow'],'busy'=>['Sibuk','badge-blue']];
                        [$slabel,$sclass] = $statusConfig[$technician->status] ?? ['—','badge-gray'];
                    @endphp
                    <span class="badge {{ $sclass }}">{{ $slabel }}</span>
                </div>
            </div>

            <hr class="border-slate-100">

            <div class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-400">Email</span>
                    <span class="font-medium text-slate-700">{{ $technician->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Sandi Login</span>
                    <span class="font-medium text-slate-700 font-mono">{{ $technician->password_plain ?? 'Tidak tersedia (hashed)' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Area Kerja</span>
                    <span class="font-medium text-slate-700">{{ $technician->area }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Outlet</span>
                    <span class="font-medium text-slate-700">{{ $technician->outlet ? $technician->outlet->name : 'Freelance' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Rating</span>
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span class="font-semibold text-slate-700">{{ number_format($technician->rating, 1) }} / 5.0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bookings / Workload History --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Riwayat Penugasan (Bookings)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar pesanan layanan yang ditugaskan kepada teknisi ini.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Booking Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Jadwal</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($technician->bookings as $b)
                        <tr class="table-row">
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-semibold text-slate-700">{{ $b->booking_code }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="font-medium text-slate-700">{{ $b->customer ? $b->customer->name : '-' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-xs text-slate-600">{{ $b->scheduled_at ? $b->scheduled_at->format('d M Y, H:i') : '-' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusBadge = ['completed'=>['Selesai','badge-green'],'in_progress'=>['Proses','badge-blue'],'pending'=>['Pending','badge-yellow'],'confirmed'=>['Konfirmasi','badge-purple'],'cancelled'=>['Batal','badge-red'],'assigned'=>['Ditugaskan','badge-gray']];
                                    [$label,$class] = $statusBadge[$b->status] ?? ['—','badge-gray'];
                                @endphp
                                <span class="badge {{ $class }}">{{ $label }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">Tidak ada riwayat penugasan ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
