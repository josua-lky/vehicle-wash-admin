@extends('layouts.app')
@section('title', 'Hasil Pencarian Global')

@section('content')
<div class="p-6 space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="/dashboard" class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Dashboard
                </a>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mt-2">Hasil Pencarian Global</h1>
            <p class="text-sm text-slate-500 mt-0.5">Ditemukan hasil pencarian untuk kata kunci: <strong class="text-slate-700">"{{ $q }}"</strong></p>
        </div>
    </div>

    {{-- SEARCH RESULTS SECTION --}}
    <div class="space-y-6">

        {{-- 1. BOOKINGS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-amber-50 text-accent flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                    <h3 class="font-semibold text-slate-800">Pesanan / Bookings ({{ $bookings->count() }})</h3>
                </div>
            </div>
            @if($bookings->isEmpty())
                <div class="p-6 text-center text-slate-400 text-sm">Tidak ada pesanan yang cocok dengan kata kunci.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Booking Code</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama Pelanggan</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kendaraan</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Jadwal</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($bookings as $b)
                            <tr class="table-row">
                                <td class="px-5 py-3.5"><span class="font-mono text-xs font-semibold text-slate-700">{{ $b->booking_code }}</span></td>
                                <td class="px-4 py-3.5 text-slate-700 font-medium">{{ $b->customer->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $b->vehicle_name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-600">{{ optional($b->scheduled_at)->format('d M Y, H:i') ?? '—' }}</td>
                                <td class="px-4 py-3.5">
                                    @php 
                                    $sc = [
                                        'pending' => 'badge-yellow',
                                        'confirmed' => 'badge-purple',
                                        'assigned' => 'badge-blue',
                                        'on_way' => 'badge-blue',
                                        'in_progress' => 'badge-blue',
                                        'completed' => 'badge-green',
                                        'cancelled' => 'badge-red'
                                    ]; 
                                    @endphp
                                    <span class="badge {{ $sc[$b->status] ?? 'badge-gray' }}">{{ $b->status_label }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <a href="/bookings/{{ $b->id }}" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 2. TECHNICIANS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <h3 class="font-semibold text-slate-800">Teknisi ({{ $technicians->count() }})</h3>
                </div>
            </div>
            @if($technicians->isEmpty())
                <div class="p-6 text-center text-slate-400 text-sm">Tidak ada teknisi yang cocok dengan kata kunci.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nomor HP</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Spesialisasi</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Rating</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($technicians as $t)
                            <tr class="table-row">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                             style="background:linear-gradient(135deg,#F0C419,#E67E22);">
                                            {{ substr($t->name, 0, 1) }}
                                        </div>
                                        <span class="text-slate-700 font-semibold">{{ $t->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 font-mono text-xs">{{ $t->phone }}</td>
                                <td class="px-4 py-3.5 text-slate-600 capitalize">{{ $t->specialization }}</td>
                                <td class="px-4 py-3.5 text-slate-600">★ {{ number_format($t->rating, 1) }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="badge {{ $t->status === 'active' ? 'badge-green' : 'badge-red' }}">
                                        {{ $t->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <a href="/technicians/{{ $t->id }}" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 3. CUSTOMERS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <h3 class="font-semibold text-slate-800">Pelanggan ({{ $customers->count() }})</h3>
                </div>
            </div>
            @if($customers->isEmpty())
                <div class="p-6 text-center text-slate-400 text-sm">Tidak ada pelanggan yang cocok dengan kata kunci.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Email</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nomor HP</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($customers as $c)
                            <tr class="table-row">
                                <td class="px-5 py-3.5"><span class="text-slate-700 font-semibold">{{ $c->name }}</span></td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $c->email }}</td>
                                <td class="px-4 py-3.5 text-slate-600 font-mono text-xs">{{ $c->phone }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="badge {{ $c->status === 'active' ? 'badge-green' : 'badge-red' }}">
                                        {{ $c->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <a href="/customers/{{ $c->id }}" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
