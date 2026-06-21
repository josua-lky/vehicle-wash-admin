@extends('layouts.app')
@section('title', 'Detail Outlet - ' . $outlet->name)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/outlets" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen Outlet
        </a>
        <a href="/outlets/{{ $outlet->id }}/edit" class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#1B2337;">
            Edit Outlet
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Outlet Summary Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-slate-800">{{ $outlet->name }}</h2>
                    @php
                        $isActive = $outlet->status === 'active';
                    @endphp
                    <span class="badge {{ $isActive?'badge-green':($outlet->status==='maintenance'?'badge-yellow':'badge-red') }}">
                        {{ $isActive?'Aktif':($outlet->status==='maintenance'?'Maintenance':'Nonaktif') }}
                    </span>
                </div>
                <p class="text-sm text-slate-500">{{ $outlet->address }}</p>
                <p class="text-xs text-slate-400 mt-2">📞 {{ $outlet->phone ?? '-' }}</p>
            </div>

            <hr class="border-slate-100">

            <div class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-400">Jam Operasional</span>
                    <span class="font-medium text-slate-700">{{ substr($outlet->open_time, 0, 5) }} – {{ substr($outlet->close_time, 0, 5) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Kapasitas / Jam</span>
                    <span class="font-medium text-slate-700">{{ $outlet->capacity_per_hour }} slot</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Slot Hari Ini</span>
                    @php
                        $open = \Carbon\Carbon::parse($outlet->open_time);
                        $close = \Carbon\Carbon::parse($outlet->close_time);
                        $hours = $close->diffInHours($open);
                        $slotsToday = $outlet->status === 'active' ? ($hours * $outlet->capacity_per_hour) : 0;
                    @endphp
                    <span class="font-medium text-slate-700">{{ $slotsToday }} slot</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Total Teknisi</span>
                    <span class="font-medium text-slate-700">{{ $outlet->technicians->count() }} teknisi</span>
                </div>
            </div>
        </div>

        {{-- Technicians List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Daftar Teknisi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Teknisi yang ditempatkan pada outlet ini.</p>
            </div>
            <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                @forelse($outlet->technicians as $tech)
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white"
                             style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                            {{ strtoupper(substr($tech->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">{{ $tech->name }}</p>
                            <p class="text-xs text-slate-400">{{ $tech->phone }}</p>
                        </div>
                    </div>
                    <span class="badge {{ $tech->status === 'active' ? 'badge-green' : 'badge-gray' }} text-xs">{{ $tech->status }}</span>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 text-sm">Belum ada teknisi di outlet ini.</div>
                @endforelse
            </div>
        </div>

        {{-- Active Bookings --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Booking Aktif</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar booking aktif yang dilakukan di outlet ini.</p>
            </div>
            <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                @forelse($outlet->bookings->whereIn('status', ['pending', 'confirmed', 'assigned', 'on_way', 'in_progress']) as $book)
                <div class="p-4">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono text-xs font-semibold text-slate-700">{{ $book->booking_code }}</span>
                        <span class="badge badge-purple text-xs">{{ $book->status }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>{{ $book->customer ? $book->customer->name : '-' }}</span>
                        <span>{{ $book->scheduled_at ? $book->scheduled_at->format('d M, H:i') : '-' }}</span>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 text-sm">Tidak ada booking aktif di outlet ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
