@extends('layouts.app')
@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="p-6 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Pelanggan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Data seluruh pelanggan terdaftar</p>
        </div>
        <a href="/customers/export" class="flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg text-white" style="background:#1B2337;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Data
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['Total Pelanggan',$stats['total']??3847,'badge-blue'],['Aktif Bulan Ini',$stats['active']??1204,'badge-green'],['Pelanggan Baru',$stats['new']??142,'badge-yellow'],['Tidak Aktif',$stats['inactive']??280,'badge-red']] as [$l,$v,$b])
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ $l }}</p>
            <p class="text-3xl font-bold text-slate-800">{{ number_format($v) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" action="/customers" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, nomor HP..." class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-600 bg-slate-50">
        </div>
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <select name="sort" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
            <option value="orders" {{ request('sort') === 'orders' ? 'selected' : '' }}>Total Order</option>
        </select>
        @if(request('search') || request('status') || request('sort'))
            <a href="/customers" class="text-sm px-4 py-2 border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 flex items-center justify-center">
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kontak</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kendaraan</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Order</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                    $customers = $customers ?? collect([
                        ['id'=>1,'name'=>'Budi Santoso','email'=>'budi@email.com','phone'=>'0812-3456-7890','vehicles'=>2,'orders'=>24,'status'=>'active','joined'=>'Jan 2023'],
                        ['id'=>2,'name'=>'Siti Rahayu','email'=>'siti@email.com','phone'=>'0856-7890-1234','vehicles'=>1,'orders'=>17,'status'=>'active','joined'=>'Mar 2023'],
                        ['id'=>3,'name'=>'Andi Wijaya','email'=>'andi@email.com','phone'=>'0878-2345-6789','vehicles'=>3,'orders'=>8,'status'=>'active','joined'=>'Jun 2023'],
                        ['id'=>4,'name'=>'Dewi Kusuma','email'=>'dewi@email.com','phone'=>'0817-5678-9012','vehicles'=>1,'orders'=>31,'status'=>'active','joined'=>'Nov 2022'],
                        ['id'=>5,'name'=>'Reza Pratama','email'=>'reza@email.com','phone'=>'0823-4567-8901','vehicles'=>2,'orders'=>3,'status'=>'inactive','joined'=>'Sep 2023'],
                        ['id'=>6,'name'=>'Lina Marlina','email'=>'lina@email.com','phone'=>'0895-6789-0123','vehicles'=>1,'orders'=>12,'status'=>'active','joined'=>'Apr 2023'],
                    ]);
                    @endphp
                    @foreach($customers as $c)
                    @php
                        if (is_array($c)) {
                            $cId = $c['id'] ?? null;
                            $cName = $c['name'] ?? '-';
                            $cEmail = $c['email'] ?? '-';
                            $cPhone = $c['phone'] ?? '-';
                            $cStatus = $c['status'] ?? 'active';
                            $cJoined = $c['joined'] ?? '-';
                            $cVehicles = $c['vehicles'] ?? 0;
                            $cOrders = $c['orders'] ?? 0;
                        } else {
                            $cId = $c->id;
                            $cName = $c->name;
                            $cEmail = $c->email;
                            $cPhone = $c->phone;
                            $cStatus = $c->status;
                            $cJoined = $c->created_at ? $c->created_at->format('M Y') : '-';
                            $cVehicles = $c->vehicles()->count();
                            $cOrders = $c->bookings()->count();
                        }
                    @endphp
                    <tr class="table-row">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $avatarUrl = null;
                                    if (is_array($c)) {
                                        $photo = $c['profile_photo'] ?? null;
                                        if ($photo) {
                                            $avatarUrl = str_starts_with($photo, 'http') ? $photo : asset('storage/' . $photo);
                                        }
                                    } else {
                                        $avatarUrl = $c->avatar;
                                    }
                                    $hasCustomPhoto = is_array($c) ? (!empty($c['profile_photo'])) : (!empty($c->profile_photo));
                                @endphp
                                @if($hasCustomPhoto && $avatarUrl)
                                    <img src="{{ $avatarUrl }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="{{ $cName }}">
                                @else
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">{{ strtoupper(substr($cName,0,1)) }}</div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $cName }}</p>
                                    <p class="text-xs text-slate-400">Bergabung {{ $cJoined }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-slate-600">{{ $cEmail }}</p>
                            <p class="text-xs text-slate-400">{{ $cPhone }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-semibold text-slate-700">{{ $cVehicles }}</span>
                            <p class="text-xs text-slate-400">kendaraan</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-semibold text-slate-700">{{ $cOrders }}</span>
                            <p class="text-xs text-slate-400">pesanan</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="badge {{ $cStatus==='active'?'badge-green':'badge-red' }}">{{ $cStatus==='active'?'Aktif':'Nonaktif' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex gap-1">
                                <a href="/customers/{{ $cId }}" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                <form method="POST" action="/customers/{{ $cId }}/toggle-status">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border {{ $cStatus==='active'?'border-red-200 text-red-500':'border-green-200 text-green-500' }}">
                                        {{ $cStatus==='active'?'Blokir':'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Menampilkan {{ count($customers) }} dari {{ $totalCustomers ?? 3847 }} pelanggan</span>
            @if(isset($customers) && method_exists($customers,'links')){{ $customers->links() }}@endif
        </div>
    </div>
</div>
@endsection
