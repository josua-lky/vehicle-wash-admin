@extends('layouts.app')
@section('title', 'Manajemen Outlet')

@section('content')
<div class="p-6 space-y-5" x-data="{ showAdd: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Outlet</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola lokasi dan jadwal outlet cuci kendaraan</p>
        </div>
        <button @click="showAdd=true" class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg shadow" style="background:#F0C419; color:#1B2337;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Outlet
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['Total Outlet',$stats['total']??8,''],['Outlet Aktif',$stats['active']??6,''],['Slot Tersedia Hari Ini',$stats['available_slots']??42,''],['Utilisasi Rata-rata',$stats['utilization']??'68%','']] as [$l,$v])
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ $l }}</p>
            <p class="text-3xl font-bold text-slate-800">{{ $v }}</p>
        </div>
        @endforeach
    </div>

    {{-- Outlet Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @php
        $outlets = $outlets ?? collect([
            ['id'=>1,'name'=>'Outlet Pusat — Jakarta Selatan','address'=>'Jl. Sudirman No.45, Jakarta Selatan','phone'=>'021-1234-5678','capacity'=>5,'open'=>'07:00','close'=>'20:00','status'=>'active','rating'=>4.8,'today_orders'=>24,'technicians'=>8],
            ['id'=>2,'name'=>'Outlet Bekasi Barat','address'=>'Jl. Ahmad Yani No.12, Bekasi Barat','phone'=>'021-8765-4321','capacity'=>3,'open'=>'08:00','close'=>'19:00','status'=>'active','rating'=>4.6,'today_orders'=>15,'technicians'=>5],
            ['id'=>3,'name'=>'Outlet Tangerang City','address'=>'Jl. Merdeka No.78, Tangerang','phone'=>'021-5555-7890','capacity'=>4,'open'=>'07:00','close'=>'20:00','status'=>'active','rating'=>4.7,'today_orders'=>18,'technicians'=>6],
            ['id'=>4,'name'=>'Outlet Depok Sawangan','address'=>'Jl. Sawangan Raya No.22, Depok','phone'=>'021-7777-1234','capacity'=>3,'open'=>'08:00','close'=>'18:00','status'=>'active','rating'=>4.5,'today_orders'=>11,'technicians'=>4],
            ['id'=>5,'name'=>'Outlet Bogor Tengah','address'=>'Jl. Pajajaran No.55, Bogor','phone'=>'0251-333-4444','capacity'=>2,'open'=>'08:00','close'=>'17:00','status'=>'maintenance','rating'=>4.4,'today_orders'=>0,'technicians'=>3],
            ['id'=>6,'name'=>'Outlet Jakarta Timur','address'=>'Jl. Raya Bogor No.99, Jakarta Timur','phone'=>'021-9876-5432','capacity'=>4,'open'=>'07:00','close'=>'20:00','status'=>'active','rating'=>4.6,'today_orders'=>20,'technicians'=>6],
        ]);
        @endphp
        @foreach($outlets as $outlet)
        @php
            if (is_array($outlet)) {
                $outId = $outlet['id'] ?? '';
                $outName = $outlet['name'] ?? '-';
                $outAddress = $outlet['address'] ?? '-';
                $outPhone = $outlet['phone'] ?? '-';
                $outStatus = $outlet['status'] ?? 'active';
                $outOpen = $outlet['open'] ?? '08:00';
                $outClose = $outlet['close'] ?? '17:00';
                $outTechnicians = $outlet['technicians'] ?? 0;
                $outRating = $outlet['rating'] ?? 5.0;
                $outTodayOrders = $outlet['today_orders'] ?? 0;
                $outCapacity = $outlet['capacity'] ?? 3;
            } else {
                $outId = $outlet->id;
                $outName = $outlet->name;
                $outAddress = $outlet->address;
                $outPhone = $outlet->phone;
                $outStatus = $outlet->status;
                $outOpen = $outlet->open_time ? substr($outlet->open_time, 0, 5) : '08:00';
                $outClose = $outlet->close_time ? substr($outlet->close_time, 0, 5) : '17:00';
                $outTechnicians = $outlet->technicians_count ?? $outlet->technicians()->count();
                $outRating = 4.8;
                $outTodayOrders = $outlet->today_bookings_count ?? $outlet->bookings()->whereDate('scheduled_at', today())->count();
                $outCapacity = $outlet->capacity_per_hour;
            }
            $isActive = $outStatus === 'active';
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden {{ !$isActive?'opacity-75':'' }}">
            {{-- Header --}}
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-slate-800 text-sm">{{ $outName }}</h3>
                        </div>
                        <p class="text-xs text-slate-400">{{ $outAddress }}</p>
                        <p class="text-xs text-slate-400">📞 {{ $outPhone }}</p>
                    </div>
                    <span class="badge {{ $isActive?'badge-green':($outStatus==='maintenance'?'badge-yellow':'badge-red') }} flex-shrink-0">
                        {{ $isActive?'Aktif':($outStatus==='maintenance'?'Maintenance':'Nonaktif') }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-500">
                    <span>🕐 {{ $outOpen }}–{{ $outClose }}</span>
                    <span>👨‍🔧 {{ $outTechnicians }} teknisi</span>
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span>{{ $outRating }}</span>
                    </div>
                </div>
            </div>
            {{-- Stats --}}
            <div class="grid grid-cols-2 divide-x divide-slate-100">
                <div class="p-4 text-center">
                    <p class="text-xl font-bold text-slate-800">{{ $outTodayOrders }}</p>
                    <p class="text-xs text-slate-400">Order Hari Ini</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-xl font-bold" style="color:#F0C419;">{{ $outCapacity }}</p>
                    <p class="text-xs text-slate-400">Kapasitas/Jam</p>
                </div>
            </div>
            {{-- Actions --}}
            <div class="px-4 py-3 border-t border-slate-100 flex gap-2">
                <a href="/outlets/{{ $outId }}" class="flex-1 text-center text-xs py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">Detail</a>
                <a href="/outlets/{{ $outId }}/edit" class="flex-1 text-center text-xs py-2 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 font-medium">Edit</a>
                <a href="/slots?outlet={{ $outId }}" class="flex-1 text-center text-xs py-2 rounded-lg text-white font-medium" style="background:#1B2337;">Kelola Slot</a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Add Outlet Modal --}}
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4" @click.self="showAdd=false">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Tambah Outlet Baru</h3>
                <button @click="showAdd=false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="/outlets" class="p-6 space-y-4">
                @csrf
                <div><label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Outlet *</label>
                    <input type="text" name="name" required placeholder="Outlet Jakarta Selatan" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700"></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Alamat Lengkap *</label>
                    <textarea name="address" required rows="2" placeholder="Jl. Sudirman No.45..." class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nomor HP</label>
                        <input type="text" name="phone" placeholder="021-xxxx" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Kapasitas/Jam *</label>
                        <input type="number" name="capacity_per_hour" required min="1" max="20" value="3" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Jam Buka</label>
                        <input type="time" name="open_time" value="07:00" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Jam Tutup</label>
                        <input type="time" name="close_time" value="20:00" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700"></div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showAdd=false" class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600">Batal</button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-xl text-slate-900" style="background:#F0C419;">Simpan Outlet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
