@extends('layouts.app')
@section('title', 'Detail Pelanggan - ' . $customer->name)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/customers" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen Pelanggan
        </a>
        <form method="POST" action="/customers/{{ $customer->id }}/toggle-status">
            @csrf @method('PATCH')
            <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg border {{ $customer->status==='active'?'border-red-200 text-red-500 bg-red-50':'border-green-200 text-green-500 bg-green-50' }}">
                {{ $customer->status==='active'?'Blokir Pelanggan':'Aktifkan Pelanggan' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center text-3xl font-bold text-white mb-4"
                     style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-slate-800">{{ $customer->name }}</h2>
                <p class="text-sm text-slate-400 mt-0.5">{{ $customer->phone }}</p>
                <div class="mt-2.5">
                    <span class="badge {{ $customer->status === 'active' ? 'badge-green' : 'badge-red' }}">{{ $customer->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>

            <hr class="border-slate-100">

            <div class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-400">Email</span>
                    <span class="font-medium text-slate-700">{{ $customer->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Terdaftar</span>
                    <span class="font-medium text-slate-700">{{ $customer->created_at ? $customer->created_at->format('d M Y') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Vehicles List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Daftar Kendaraan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kendaraan yang terdaftar di akun ini.</p>
            </div>
            <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                @forelse($customer->vehicles as $veh)
                <div class="p-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                        🚗
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700">{{ $veh->brand }} {{ $veh->model }}</p>
                        <p class="text-xs text-slate-400">Pelat: {{ $veh->plate_number ?? '-' }} ({{ $veh->type === 'roda_2' ? 'Roda 2' : 'Roda 4' }})</p>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 text-sm">Belum ada kendaraan terdaftar.</div>
                @endforelse
            </div>
        </div>

        {{-- Booking History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Riwayat Booking</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar pemesanan layanan oleh pelanggan ini.</p>
            </div>
            <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                @forelse($customer->bookings as $book)
                <div class="p-4">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono text-xs font-semibold text-slate-700">{{ $book->booking_code }}</span>
                        @php
                            $statusBadge = ['completed'=>['Selesai','badge-green'],'in_progress'=>['Proses','badge-blue'],'pending'=>['Pending','badge-yellow'],'confirmed'=>['Konfirmasi','badge-purple'],'cancelled'=>['Batal','badge-red'],'assigned'=>['Ditugaskan','badge-gray']];
                            [$label,$class] = $statusBadge[$book->status] ?? ['—','badge-gray'];
                        @endphp
                        <span class="badge {{ $class }} text-xs">{{ $label }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>{{ $book->package ? $book->package->name : '-' }}</span>
                        <span>{{ $book->scheduled_at ? $book->scheduled_at->format('d M Y, H:i') : '-' }}</span>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 text-sm">Belum ada riwayat booking.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
