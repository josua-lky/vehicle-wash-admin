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
            <select x-model="selectedOutlet" @change="history.replaceState(null, '', '/slots' + (selectedOutlet ? '?outlet_id=' + selectedOutlet : ''))" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white">
                <option value="">Semua Outlet</option>
                @foreach($outlets ?? [] as $o)
                <option value="{{ $o->id }}">{{ $o->name }}</option>
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

            {{-- Selected date info & Hourly Slots Table --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-800 text-sm">Slot & Jadwal Booking</h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="selectedDay ? monthName+' '+selectedDay+', '+year : 'Pilih tanggal di kalender'"></p>
                    </div>
                    <template x-if="selectedOutlet">
                        <button @click="selectedOutlet = ''; history.replaceState(null, '', '/slots')" class="text-xs font-semibold text-amber-600 hover:text-amber-700">Ubah Outlet</button>
                    </template>
                </div>

                <div x-show="!selectedOutlet" class="space-y-4 py-2">
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl text-center">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h4 class="text-sm font-bold text-slate-700">Pilih Outlet Terlebih Dahulu</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-[250px] mx-auto">Silakan pilih salah satu outlet di bawah ini untuk melihat jadwal slot secara mendetail.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5">
                        <template x-for="o in outlets" :key="o.id">
                            <button @click="selectedOutlet = String(o.id); history.replaceState(null, '', '/slots?outlet_id=' + o.id)"
                                    class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all text-left shadow-sm w-full">
                                <div class="space-y-0.5">
                                    <h5 class="font-bold text-slate-800 text-xs" x-text="o.name"></h5>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="`${o.open_time.substring(0, 5)} - ${o.close_time.substring(0, 5)}`"></span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            <span x-text="`${o.capacity_per_hour} slot/jam`"></span>
                                        </span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </template>
                    </div>
                </div>

                <div x-show="selectedOutlet" class="space-y-3 max-h-[450px] overflow-y-auto pr-1">
                    <template x-for="hour in outletHours" :key="hour">
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-700 bg-slate-200 px-2 py-1 rounded" x-text="hour"></span>
                                    <span class="text-xs font-semibold"
                                          :class="getHourBookings(hour).length >= getOutletCapacity() ? 'text-red-600' : (getHourBookings(hour).length > 0 ? 'text-amber-600' : 'text-green-600')"
                                          x-text="getHourBookings(hour).length + '/' + getOutletCapacity() + ' Terisi'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge" 
                                          :class="getHourBookings(hour).length >= getOutletCapacity() ? 'badge-red' : (getHourBookings(hour).length > 0 ? 'badge-yellow' : 'badge-green')"
                                          x-text="getHourBookings(hour).length >= getOutletCapacity() ? 'Penuh' : (getOutletCapacity() - getHourBookings(hour).length) + ' Tersedia'"></span>
                                    
                                    <template x-if="getHourBookings(hour).length < getOutletCapacity()">
                                        <button @click="prepareBookingForHour(hour)"
                                                class="px-2.5 py-1 rounded text-[10px] font-bold text-slate-900 shadow hover:opacity-90 transition-opacity"
                                                style="background:#F0C419;">
                                            Reservasi
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="space-y-1.5 pl-2">
                                <template x-for="b in getHourBookings(hour)" :key="b.id">
                                    <div class="p-2 bg-white rounded-lg border border-slate-200 text-xs space-y-1 shadow-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="font-mono font-bold text-slate-800 text-[11px]" x-text="b.booking_code"></span>
                                            <span class="badge" :class="{
                                                'badge-yellow': b.status === 'pending',
                                                'badge-purple': b.status === 'confirmed',
                                                'badge-blue': ['assigned', 'on_way', 'in_progress'].includes(b.status),
                                                'badge-green': b.status === 'completed',
                                                'badge-red': b.status === 'cancelled'
                                            }" x-text="b.status_label"></span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 grid grid-cols-2 gap-x-2 gap-y-0.5">
                                            <div>Pelanggan: <span class="font-bold text-slate-700" x-text="b.customer_name"></span></div>
                                            <div>Tipe: <span class="font-semibold text-slate-700 uppercase" x-text="b.vehicle_type === 'roda_2' ? 'Motor' : 'Mobil'"></span></div>
                                            <div class="col-span-2">Kendaraan: <span class="font-semibold text-slate-700" x-text="b.vehicle_name"></span></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="getHourBookings(hour).length === 0">
                                    <div class="text-slate-400 text-xs italic flex items-center gap-1.5 py-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Slot Kosong (Tersedia)
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Capacity Summary --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800 mb-4" x-text="'Kapasitas ' + (selectedDay ? monthName+' '+selectedDay+', '+year : 'Hari Ini')"></h3>
                <div class="text-center mb-4">
                    <p class="text-3xl font-bold text-slate-800">
                        <span x-text="selectedDateCapacityStats.booked"></span><span class="text-slate-300 text-xl">/<span x-text="selectedDateCapacityStats.capacity"></span></span>
                    </p>
                    <p class="text-xs text-slate-400 mt-1">slot terisi pada tanggal ini</p>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden mb-2">
                    <div class="h-full rounded-full transition-all" :style="'width: ' + selectedDateCapacityStats.pct + '%; background:linear-gradient(to right,#F0C419,#E67E22);'"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span class="text-green-600 font-medium" x-text="selectedDateCapacityStats.available + ' tersedia'"></span>
                    <span class="text-amber-600 font-medium" x-text="selectedDateCapacityStats.pct + '% terisi'"></span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="p-3 rounded-xl text-center" style="background:#F8FAFC;">
                        <p class="text-lg font-bold text-slate-800" x-text="selectedDateCapacityStats.available"></p>
                        <p class="text-xs text-slate-400">Slot Tersedia</p>
                    </div>
                    <div class="p-3 rounded-xl text-center" style="background:#FEF9EC;">
                        <p class="text-lg font-bold" style="color:#F0C419;" x-text="selectedDateCapacityStats.booked"></p>
                        <p class="text-xs text-slate-400">Sudah Dipesan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK SLOTS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800" x-text="'Daftar Booking Slot Tanggal ' + (selectedDay ? monthName+' '+selectedDay+', '+year : 'Hari Ini')"></h3>
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
                    <template x-for="tb in selectedDateBookings" :key="tb.id">
                        <tr class="table-row">
                            <td class="px-5 py-3.5"><span class="font-mono text-xs font-semibold text-slate-700" x-text="tb.booking_code"></span></td>
                            <td class="px-4 py-3.5 text-slate-700" x-text="tb.customer_name"></td>
                            <td class="px-4 py-3.5 text-slate-600 text-xs">
                                <span x-text="tb.vehicle_name"></span> 
                                <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 uppercase" x-text="tb.vehicle_type === 'roda_2' ? 'Motor' : 'Mobil'"></span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-600" x-text="tb.scheduled_time"></td>
                            <td class="px-4 py-3.5">
                                <span class="badge" :class="{
                                    'badge-yellow': tb.status === 'pending',
                                    'badge-purple': tb.status === 'confirmed',
                                    'badge-blue': ['assigned', 'on_way', 'in_progress'].includes(tb.status),
                                    'badge-green': tb.status === 'completed',
                                    'badge-red': tb.status === 'cancelled',
                                    'badge-gray': !['pending', 'confirmed', 'assigned', 'on_way', 'in_progress', 'completed', 'cancelled'].includes(tb.status)
                                }" x-text="tb.status_label"></span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex gap-1">
                                    <a :href="'/bookings/' + tb.id" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                    <template x-if="tb.status !== 'completed' && tb.status !== 'cancelled'">
                                        <form method="POST" :action="'/bookings/' + tb.id">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button class="text-xs px-2.5 py-1.5 rounded-lg text-white" style="background:#10B981;">Selesai</button>
                                        </form>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="selectedDateBookings.length === 0">
                        <td colspan="6" class="text-center py-8 text-slate-400 text-sm">Tidak ada booking untuk slot cuci pada tanggal ini.</td>
                    </tr>
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
        bookings: @json($bookingsData),
        get monthName() {
            return ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][this.month];
        },
        getOutletHoursCount(outlet) {
            if (!outlet) return 0;
            const openTime = outlet.open_time || '07:00';
            const closeTime = outlet.close_time || '20:00';
            const [openH] = openTime.split(':').map(Number);
            const [closeH] = closeTime.split(':').map(Number);
            return Math.max(0, closeH - openH + 1);
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
                const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                
                let capacity = 0;
                let booked = 0;

                if (this.selectedOutlet) {
                    const outlet = this.outlets.find(o => String(o.id) === String(this.selectedOutlet));
                    if (outlet) {
                        capacity = this.getOutletHoursCount(outlet) * (outlet.capacity_per_hour || 3);
                        booked = this.bookings.filter(b => {
                            return b.scheduled_date === dateStr &&
                                   String(b.outlet_id) === String(this.selectedOutlet);
                        }).length;
                    }
                } else {
                    this.outlets.forEach(outlet => {
                        capacity += this.getOutletHoursCount(outlet) * (outlet.capacity_per_hour || 3);
                    });
                    booked = this.bookings.filter(b => b.scheduled_date === dateStr).length;
                }

                if (capacity > 0) {
                    if (booked === 0) {
                        statuses.push('available');
                    } else if (booked >= capacity) {
                        statuses.push('full');
                    } else {
                        statuses.push('busy');
                    }
                } else {
                    statuses.push('available');
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
        get selectedDateCapacityStats() {
            if (!this.selectedDay) return { capacity: 0, booked: 0, available: 0, pct: 0 };
            const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(this.selectedDay).padStart(2, '0')}`;
            
            let capacity = 0;
            let booked = 0;

            if (this.selectedOutlet) {
                const outlet = this.outlets.find(o => String(o.id) === String(this.selectedOutlet));
                if (outlet) {
                    capacity = this.getOutletHoursCount(outlet) * (outlet.capacity_per_hour || 3);
                    booked = this.bookings.filter(b => {
                        return b.scheduled_date === dateStr &&
                               String(b.outlet_id) === String(this.selectedOutlet);
                    }).length;
                }
            } else {
                this.outlets.forEach(outlet => {
                    capacity += this.getOutletHoursCount(outlet) * (outlet.capacity_per_hour || 3);
                });
                booked = this.bookings.filter(b => b.scheduled_date === dateStr).length;
            }

            const available = Math.max(0, capacity - booked);
            const pct = capacity > 0 ? Math.round((booked / capacity) * 100) : 0;
            return { capacity, booked, available, pct };
        },
        get selectedDateBookings() {
            if (!this.selectedDay) return [];
            const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(this.selectedDay).padStart(2, '0')}`;
            return this.bookings.filter(b => {
                const matchesDate = b.scheduled_date === dateStr;
                const matchesOutlet = this.selectedOutlet ? String(b.outlet_id) === String(this.selectedOutlet) : true;
                return matchesDate && matchesOutlet && b.status !== 'completed' && b.status !== 'cancelled';
            });
        },
        get outletHours() {
            if (!this.selectedOutlet) return [];
            const outlet = this.outlets.find(o => String(o.id) === String(this.selectedOutlet));
            if (!outlet) return [];

            const openTime = outlet.open_time || '07:00';
            const closeTime = outlet.close_time || '20:00';
            const [openH] = openTime.split(':').map(Number);
            const [closeH] = closeTime.split(':').map(Number);

            const hours = [];
            for (let h = openH; h <= closeH; h++) {
                const timeStr = String(h).padStart(2, '0') + ':00';
                hours.push(timeStr);
            }
            return hours;
        },
        getOutletCapacity() {
            if (!this.selectedOutlet) return 3;
            const outlet = this.outlets.find(o => String(o.id) === String(this.selectedOutlet));
            return outlet ? outlet.capacity_per_hour : 3;
        },
        getHourBookings(hour) {
            if (!this.selectedDay || !this.selectedOutlet) return [];
            const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(this.selectedDay).padStart(2, '0')}`;
            return this.bookings.filter(b => {
                return b.scheduled_date === dateStr &&
                       String(b.outlet_id) === String(this.selectedOutlet) &&
                       b.scheduled_time.substring(0, 5) === hour.substring(0, 5) &&
                       b.status !== 'completed' && b.status !== 'cancelled';
            });
        },
        prepareBookingForHour(hour) {
            const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(this.selectedDay).padStart(2, '0')}`;
            const outlet = this.outlets.find(o => String(o.id) === String(this.selectedOutlet));
            
            const existingSlot = this.activeDaySlots.find(s => s.time === hour.substring(0, 5) && String(s.outlet_id) === String(this.selectedOutlet));
            
            this.selectedSlot = {
                id: existingSlot ? existingSlot.id : '',
                outlet_id: this.selectedOutlet,
                outlet_name: outlet ? outlet.name : '',
                date: dateStr,
                time: hour
            };
            this.showBookSlot = true;
        },
        selectDate(cell) { 
            if (cell.day) { 
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
        prevMonth() { 
            if (this.month === 0) { 
                this.month = 11; 
                this.year--; 
            } else {
                this.month--; 
            }
            this.selectedDay = 1;
            this.selectedGroup = null;
            this.selectedSlot = null;
        },
        nextMonth() { 
            if (this.month === 11) { 
                this.month = 0; 
                this.year++; 
            } else {
                this.month++; 
            }
            this.selectedDay = 1;
            this.selectedGroup = null;
            this.selectedSlot = null;
        },
        gotoToday() { 
            const t = new Date(); 
            this.month = t.getMonth(); 
            this.year = t.getFullYear(); 
            this.selectedDay = t.getDate(); 
            this.selectedGroup = null;
            this.selectedSlot = null;
        },
    }
}
</script>
@endpush
