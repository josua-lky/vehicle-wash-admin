@extends('layouts.app')
@section('title', 'Manajemen Slot Cuci')

@section('content')
<div class="p-6 space-y-5" x-data="slotPage()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Slot Cuci</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola ketersediaan slot waktu di setiap outlet</p>
        </div>
        <div class="flex gap-2">
            <select x-model="selectedOutlet" @change="window.location.href = '/slots?outlet_id=' + selectedOutlet" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white">
                <option value="">Semua Outlet</option>
                @foreach($outlets ?? [] as $o)
                <option value="{{ $o->id }}" {{ request('outlet_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                @endforeach
            </select>
            <button @click="showAddSlot=true"
                    class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg shadow transition-colors"
                    style="background:#F0C419; color:#1B2337;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Atur Slot
            </button>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- CALENDAR --}}
        <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">

            {{-- Month navigation --}}
            <div class="flex items-center justify-between mb-6">
                <button @click="prevMonth()" class="p-2 rounded-xl hover:bg-slate-100 text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h2 class="text-base font-bold text-slate-800" x-text="monthName+' '+year"></h2>
                <div class="flex items-center gap-2">
                    <button @click="gotoToday()" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Hari Ini</button>
                    <button @click="viewMode='month'" :class="viewMode==='month'?'text-white':'text-slate-600 border border-slate-200'"
                            class="text-xs px-3 py-1.5 rounded-lg" :style="viewMode==='month'?'background:#1B2337':''">Bulan</button>
                    <button @click="nextMonth()" class="p-2 rounded-xl hover:bg-slate-100 text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- Day headers --}}
            <div class="grid grid-cols-7 gap-1 mb-2">
                <template x-for="day in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']" :key="day">
                    <div class="text-center text-xs font-semibold text-slate-400 py-2" x-text="day"></div>
                </template>
            </div>

            {{-- Calendar grid --}}
            <div class="grid grid-cols-7 gap-1">
                <template x-for="(cell, i) in calendarCells" :key="i">
                    <button @click="selectDate(cell)"
                            :disabled="!cell.day"
                            class="aspect-square flex flex-col items-center justify-center rounded-xl text-sm transition-all"
                            :class="{
                                'opacity-0 pointer-events-none': !cell.day,
                                'ring-2 ring-offset-1 font-bold text-white': cell.day === selectedDay,
                                'hover:bg-slate-50': cell.day && cell.day !== selectedDay,
                                'text-slate-300': cell.isPast,
                                'text-slate-700': !cell.isPast && cell.day !== selectedDay,
                            }"
                            :style="cell.day === selectedDay ? 'background:#1B2337; ring-color:#1B2337' : ''">
                        <span x-text="cell.day || ''"></span>
                        <div x-show="cell.day && !cell.isPast" class="flex gap-0.5 mt-0.5">
                            <template x-for="s in cell.status" :key="s">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :style="s==='available'?'background:#10B981':s==='busy'?'background:#F0C419':'background:#EF4444'"></span>
                            </template>
                        </div>
                    </button>
                </template>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-5 mt-5 pt-4 border-t border-slate-100 text-xs text-slate-500">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span> Tersedia</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background:#F0C419;"></span> Sebagian Terisi</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Penuh</div>
            </div>
        </div>

        {{-- RIGHT: Time Slots --}}
        <div class="space-y-4">

            {{-- Selected date info --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-800 text-sm">Slot Waktu Cuci</h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="selectedDay ? monthName+' '+selectedDay+', '+year : 'Pilih tanggal di kalender'"></p>
                    </div>
                </div>

                {{-- Time slots list --}}
                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    <template x-for="group in groupedActiveDaySlots" :key="group.time">
                        <div class="flex items-center justify-between p-3 rounded-xl border transition-all hover:shadow-sm cursor-pointer"
                             :style="selectedGroup && selectedGroup.time === group.time ? 'background:#F3F4F6; border-color:#9CA3AF;' : (group.status === 'full' ? 'background: #FEF2F2; border-color: #FECACA;' : (group.status === 'partial' ? 'background: #FEF9EC; border-color: #FDE68A;' : 'background: #ECFDF5; border-color: #A7F3D0;'))"
                             @click="selectGroup(group)">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full"
                                     :style="group.status === 'full' ? 'background: #EF4444;' : (group.status === 'partial' ? 'background: #F0C419;' : 'background: #10B981;')"></div>
                                <span class="text-sm font-semibold text-slate-700" x-text="group.time"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium" :class="group.status === 'full' ? 'text-red-600' : (group.status === 'partial' ? 'text-amber-600' : 'text-green-600')">
                                    <span x-text="group.booked"></span>/<span x-text="group.capacity"></span>
                                </span>
                                <p class="text-xs" :class="group.status === 'full' ? 'text-red-600' : (group.status === 'partial' ? 'text-amber-600' : 'text-green-600')" x-text="group.status === 'full' ? 'Penuh' : (group.status === 'partial' ? 'Sebagian' : 'Tersedia')"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="groupedActiveDaySlots.length === 0" class="text-center py-8 text-sm text-slate-400">
                        Tidak ada slot cuci pada tanggal ini.
                    </div>
                </div>

                {{-- Grouped Slot details --}}
                <div x-show="selectedGroup" class="mt-4 space-y-3 pt-3 border-t border-slate-100" x-cloak>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Detail Outlet untuk Jam <span x-text="selectedGroup ? selectedGroup.time : ''"></span>:
                    </div>
                    
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        <template x-for="item in (selectedGroup ? selectedGroup.slots : [])" :key="item.id">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-700" x-text="item.outlet_name"></span>
                                    <span class="badge" :class="item.status === 'full' ? 'badge-red' : (item.status === 'partial' ? 'badge-yellow' : 'badge-green')" x-text="item.booked + '/' + item.capacity + ' Terisi'"></span>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <!-- Reservasi Slot Button -->
                                    <button @click="prepareBooking(item)"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-slate-900 shadow hover:opacity-90 transition-opacity"
                                            style="background:#F0C419;"
                                            :disabled="item.status === 'full'"
                                            :class="item.status === 'full' ? 'opacity-50 cursor-not-allowed' : ''">
                                        Reservasi
                                    </button>
                                    
                                    <!-- Hapus Slot Form -->
                                    <form :action="'/slots/' + item.id" method="POST" class="inline" @submit="return confirm('Apakah Anda yakin ingin menghapus slot ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-2 py-1.5 rounded-lg text-[11px] font-semibold text-white bg-red-600 hover:bg-red-700 shadow transition-colors"
                                                x-show="item.booked === 0">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Capacity Summary --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Kapasitas Hari Ini</h3>
                @php
                $totalCap = $todayStats['capacity'] ?? 0;
                $booked = $todayStats['booked'] ?? 0;
                $avail = max(0, $totalCap - $booked);
                $pct = $totalCap > 0 ? round($booked / $totalCap * 100) : 0;
                @endphp
                <div class="text-center mb-4">
                    <p class="text-3xl font-bold text-slate-800">{{ $booked }}<span class="text-slate-300 text-xl">/{{ $totalCap }}</span></p>
                    <p class="text-xs text-slate-400 mt-1">slot terisi hari ini</p>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden mb-2">
                    <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%; background:linear-gradient(to right,#F0C419,#E67E22);"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span class="text-green-600 font-medium">{{ $avail }} tersedia</span>
                    <span class="text-amber-600 font-medium">{{ $pct }}% terisi</span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="p-3 rounded-xl text-center" style="background:#F8FAFC;">
                        <p class="text-lg font-bold text-slate-800">{{ $avail }}</p>
                        <p class="text-xs text-slate-400">Slot Tersedia</p>
                    </div>
                    <div class="p-3 rounded-xl text-center" style="background:#FEF9EC;">
                        <p class="text-lg font-bold" style="color:#F0C419;">{{ $booked }}</p>
                        <p class="text-xs text-slate-400">Sudah Dipesan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK SLOTS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Daftar Booking Slot Hari Ini</h3>
            <a href="/bookings?service_type=outlet" class="text-xs font-medium" style="color:#F0C419;">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Booking ID</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama User</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Jenis Kendaraan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Jadwal</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($todayBookings ?? [] as $tb)
                    <tr class="table-row">
                        <td class="px-5 py-3.5"><span class="font-mono text-xs font-semibold text-slate-700">{{ $tb->booking_code }}</span></td>
                        <td class="px-4 py-3.5 text-slate-700">{{ $tb->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-slate-600 text-xs">
                            {{ $tb->vehicle_name }} 
                            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 uppercase">{{ str_replace('_', ' ', $tb->vehicle_type) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-slate-600">{{ $tb->scheduled_at->format('d M Y, H:i') }}</td>
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
                            <span class="badge {{ $sc[$tb->status] ?? 'badge-gray' }}">{{ $tb->status_label }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex gap-1">
                                <a href="/bookings/{{ $tb->id }}" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                @if($tb->status !== 'completed' && $tb->status !== 'cancelled')
                                <form method="POST" action="/bookings/{{ $tb->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button class="text-xs px-2.5 py-1.5 rounded-lg text-white" style="background:#10B981;">Selesai</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-400 text-sm">Tidak ada booking untuk slot cuci hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ATUR SLOT BARU --}}
    <div x-show="showAddSlot" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-slate-100 mx-4" @click.away="showAddSlot=false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-lg">Atur Slot Baru</h3>
                <button @click="showAddSlot=false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('slots.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Outlet</label>
                        <select name="outlet_id" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required>
                            @foreach($outlets ?? [] as $o)
                            <option value="{{ $o->id }}" {{ request('outlet_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                        <input type="date" name="slot_date" :value="`${year}-${String(month + 1).padStart(2, '0')}-${String(selectedDay).padStart(2, '0')}`" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Waktu Slot (Jam)</label>
                        <input type="time" name="slot_time" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required placeholder="Contoh: 09:00">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kapasitas</label>
                        <input type="number" name="capacity" min="1" max="20" value="{{ $selectedOutlet ? $selectedOutlet->capacity_per_hour : 3 }}" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="showAddSlot=false" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg shadow text-slate-900" style="background:#F0C419;">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: RESERVASI SLOT --}}
    <div x-show="showBookSlot" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-slate-100 mx-4 overflow-y-auto max-h-[90vh]" @click.away="showBookSlot=false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-lg">Reservasi Slot Cuci</h3>
                <button @click="showBookSlot=false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 space-y-1">
                <div class="flex justify-between">
                    <span>Tanggal & Waktu:</span>
                    <span class="font-semibold text-slate-800" x-text="selectedSlot ? (selectedSlot.date + ' ' + selectedSlot.time) : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span>Outlet:</span>
                    <span class="font-semibold text-slate-800" x-text="selectedSlot ? selectedSlot.outlet_name : ''"></span>
                </div>
            </div>

            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="from_slots" value="true">
                <input type="hidden" name="service_type" value="outlet">
                <input type="hidden" name="outlet_id" :value="selectedSlot ? selectedSlot.outlet_id : ''">
                <input type="hidden" name="outlet_slot_id" :value="selectedSlot ? selectedSlot.id : ''">
                <input type="hidden" name="scheduled_at" :value="selectedSlot ? (selectedSlot.date + ' ' + selectedSlot.time) : ''">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pelanggan</label>
                        <select name="customer_id" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required>
                            <option value="">Pilih Pelanggan</option>
                            @foreach($customers ?? [] as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Kendaraan</label>
                        <input type="text" name="vehicle_name" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required placeholder="Contoh: Honda Vario, Toyota Avanza">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Kendaraan</label>
                        <select name="vehicle_type" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required>
                            <option value="roda_2">Motor (Roda 2)</option>
                            <option value="roda_4">Mobil (Roda 4)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Paket Cuci</label>
                        <select name="package_id" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" required>
                            <option value="">Pilih Paket</option>
                            @foreach($packages ?? [] as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white" placeholder="Catatan khusus..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="showBookSlot=false" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg shadow text-slate-900" style="background:#F0C419;">Konfirmasi Booking</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function slotPage() {
    const today = new Date();
    return {
        month: today.getMonth(),
        year: today.getFullYear(),
        selectedDay: today.getDate(),
        selectedSlot: null,
        selectedGroup: null,
        showAddSlot: false,
        showBookSlot: false,
        selectedOutlet: '{{ request('outlet_id') }}',
        viewMode: 'month',
        slots: @json($slots),
        outlets: @json($outlets),
        get monthName() {
            return ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][this.month];
        },
        get calendarCells() {
            const firstDay = new Date(this.year, this.month, 1).getDay();
            const days = new Date(this.year, this.month + 1, 0).getDate();
            const cells = [];
            for (let i = 0; i < firstDay; i++) cells.push({ day: null });
            const now = new Date();
            for (let d = 1; d <= days; d++) {
                const isPast = new Date(this.year, this.month, d) < new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const statuses = [];
                if (!isPast) {
                    const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    let daySlots = this.slots.filter(s => s.slot_date.substring(0, 10) === dateStr);
                    if (this.selectedOutlet) {
                        daySlots = daySlots.filter(s => String(s.outlet_id) === String(this.selectedOutlet));
                    }
                    if (daySlots.length > 0) {
                        const totalCapacity = daySlots.reduce((sum, s) => sum + s.capacity, 0);
                        const totalBooked = daySlots.reduce((sum, s) => sum + s.booked_count, 0);
                        if (totalBooked === 0) {
                            statuses.push('available');
                        } else if (totalBooked >= totalCapacity) {
                            statuses.push('full');
                        } else {
                            statuses.push('busy');
                        }
                    }
                }
                cells.push({ day: d, isPast, status: statuses });
            }
            return cells;
        },
        get activeDaySlots() {
            if (!this.selectedDay) return [];
            const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(this.selectedDay).padStart(2, '0')}`;
            let daySlots = this.slots.filter(s => s.slot_date.substring(0, 10) === dateStr);
            if (this.selectedOutlet) {
                daySlots = daySlots.filter(s => String(s.outlet_id) === String(this.selectedOutlet));
            }
            return daySlots.map(s => {
                let status = 'available';
                if (s.booked_count >= s.capacity) status = 'full';
                else if (s.booked_count > 0) status = 'partial';
                return {
                    id: s.id,
                    time: s.slot_time.substring(0, 5),
                    capacity: s.capacity,
                    booked: s.booked_count,
                    status: status,
                    outlet_id: s.outlet_id,
                    outlet_name: s.outlet ? s.outlet.name : '',
                    date: s.slot_date.substring(0, 10)
                };
            });
        },
        get groupedActiveDaySlots() {
            const list = this.activeDaySlots;
            const groups = {};
            list.forEach(slot => {
                if (!groups[slot.time]) {
                    groups[slot.time] = {
                        time: slot.time,
                        slots: [],
                        totalCapacity: 0,
                        totalBooked: 0
                    };
                }
                groups[slot.time].slots.push(slot);
                groups[slot.time].totalCapacity += slot.capacity;
                groups[slot.time].totalBooked += slot.booked;
            });
            
            return Object.values(groups).map(g => {
                let status = 'available';
                if (g.totalBooked >= g.totalCapacity) status = 'full';
                else if (g.totalBooked > 0) status = 'partial';
                return {
                    time: g.time,
                    slots: g.slots,
                    capacity: g.totalCapacity,
                    booked: g.totalBooked,
                    status: status
                };
            }).sort((a, b) => a.time.localeCompare(b.time));
        },
        selectDate(cell) { 
            if (cell.day && !cell.isPast) { 
                this.selectedDay = cell.day; 
                this.selectedSlot = null; 
                this.selectedGroup = null;
            } 
        },
        selectGroup(group) {
            this.selectedGroup = group;
            this.selectedSlot = null;
        },
        prepareBooking(slot) {
            this.selectedSlot = slot;
            this.showBookSlot = true;
        },
        prevMonth() { if (this.month === 0) { this.month = 11; this.year--; } else this.month--; },
        nextMonth() { if (this.month === 11) { this.month = 0; this.year++; } else this.month++; },
        gotoToday() { const t = new Date(); this.month = t.getMonth(); this.year = t.getFullYear(); this.selectedDay = t.getDate(); },
    }
}
</script>
@endpush
