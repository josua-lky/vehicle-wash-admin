@extends('layouts.app')
@section('title', 'Manajemen Booking')

@section('content')
<div class="p-6 space-y-5" x-data="bookingPage()" @open-booking-detail.window="openDetail($event.detail.id, $event.detail.code)">

    {{-- ── HEADER ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Booking</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola seluruh pesanan layanan cuci kendaraan</p>
        </div>
        <a href="/bookings/create" class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg text-white shadow"
           style="background:#1B2337;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Booking
        </a>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div id="stats-cards-container" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $bookingStats = [
            ['label'=>'Total Bookings Masuk','value'=>$stats['total']??142,'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','color'=>'#F0C419','bg'=>'#FEF9EC'],
            ['label'=>'Selesai','value'=>$stats['completed']??89,'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#10B981','bg'=>'#ECFDF5'],
            ['label'=>'Dalam Proses','value'=>$stats['in_progress']??31,'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#3B82F6','bg'=>'#EFF6FF'],
            ['label'=>'Dibatalkan','value'=>$stats['cancelled']??4,'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#EF4444','bg'=>'#FEF2F2'],
        ];
        @endphp
        @foreach($bookingStats as $s)
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 mb-1">{{ $s['label'] }}</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $s['value'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:{{ $s['bg'] }};">
                    <svg class="w-5 h-5" style="color:{{ $s['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── FILTER BAR ── --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
        <form method="GET" action="/bookings" class="flex flex-wrap items-center gap-3">
            {{-- Date range --}}
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input type="text" name="date_from" value="{{ request('date_from','2024-01-01') }}"
                       class="text-sm text-slate-600 bg-transparent w-24" placeholder="Dari tanggal">
                <span class="text-slate-300">—</span>
                <input type="text" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}"
                       class="text-sm text-slate-600 bg-transparent w-24" placeholder="Sampai tanggal">
            </div>

            {{-- Status filter --}}
            <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Dikonfirmasi</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Selesai</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Dibatalkan</option>
            </select>

            {{-- Service type --}}
            <select name="service_type" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
                <option value="">Semua Layanan</option>
                <option value="home" {{ request('service_type')=='home'?'selected':'' }}>Home Service</option>
                <option value="outlet" {{ request('service_type')=='outlet'?'selected':'' }}>Outlet</option>
            </select>

            {{-- Specialization filter --}}
            <select name="vehicle_type" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
                <option value="">Semua Spesialisasi</option>
                <option value="motor" {{ request('vehicle_type')=='motor'?'selected':'' }}>Motor</option>
                <option value="mobil" {{ request('vehicle_type')=='mobil'?'selected':'' }}>Mobil</option>
            </select>

            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID booking, nama, nomor HP..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-600 bg-slate-50">
            </div>

            {{-- Export buttons --}}
            <div class="flex gap-2 ml-auto">
                <a href="/bookings/export?format=excel" class="flex items-center gap-1.5 text-sm font-medium px-3 py-2 rounded-lg text-white"
                   style="background:#10B981;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    {{-- ── TABLE + CHART ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Booking Table --}}
        <div id="booking-table-container" class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide whitespace-nowrap">Booking ID</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama User</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide whitespace-nowrap">Spesialisasi</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Jadwal</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                        $bookings = $bookings ?? collect([
                            ['id'=>'VW-2024-0841','code'=>'VW-2024-0841','customer'=>'Budi Santoso','vehicle'=>'Toyota Avanza','vehicle_type'=>'Roda 4','scheduled_at'=>'13 Nov 2024, 09:00','status'=>'completed','service'=>'Outlet','amount'=>55000],
                            ['id'=>'VW-2024-0840','code'=>'VW-2024-0840','customer'=>'Siti Rahayu','vehicle'=>'Honda Beat','vehicle_type'=>'Roda 2','scheduled_at'=>'13 Nov 2024, 08:30','status'=>'in_progress','service'=>'Home Service','amount'=>25000],
                            ['id'=>'VW-2024-0839','code'=>'VW-2024-0839','customer'=>'Andi Wijaya','vehicle'=>'Yamaha NMAX','vehicle_type'=>'Roda 2','scheduled_at'=>'13 Nov 2024, 07:45','status'=>'pending','service'=>'Outlet','amount'=>15000],
                            ['id'=>'VW-2024-0838','code'=>'VW-2024-0838','customer'=>'Dewi Kusuma','vehicle'=>'Honda CRV','vehicle_type'=>'Roda 4','scheduled_at'=>'12 Nov 2024, 16:00','status'=>'confirmed','service'=>'Home Service','amount'=>90000],
                            ['id'=>'VW-2024-0837','code'=>'VW-2024-0837','customer'=>'Reza Pratama','vehicle'=>'Mitsubishi Pajero','vehicle_type'=>'Roda 4','scheduled_at'=>'12 Nov 2024, 14:20','status'=>'cancelled','service'=>'Home Service','amount'=>90000],
                            ['id'=>'VW-2024-0836','code'=>'VW-2024-0836','customer'=>'Lina Marlina','vehicle'=>'Honda Vario','vehicle_type'=>'Roda 2','scheduled_at'=>'12 Nov 2024, 11:00','status'=>'completed','service'=>'Outlet','amount'=>25000],
                            ['id'=>'VW-2024-0835','code'=>'VW-2024-0835','customer'=>'Hendra Gunawan','vehicle'=>'Suzuki Ertiga','vehicle_type'=>'Roda 4','scheduled_at'=>'12 Nov 2024, 10:15','status'=>'completed','service'=>'Outlet','amount'=>55000],
                            ['id'=>'VW-2024-0834','code'=>'VW-2024-0834','customer'=>'Maya Sari','vehicle'=>'Honda Scoopy','vehicle_type'=>'Roda 2','scheduled_at'=>'11 Nov 2024, 15:30','status'=>'completed','service'=>'Home Service','amount'=>25000],
                        ]);
                        $statusBadge = ['completed'=>['Selesai','badge-green'],'in_progress'=>['Proses','badge-blue'],'pending'=>['Pending','badge-yellow'],'confirmed'=>['Konfirmasi','badge-purple'],'cancelled'=>['Batal','badge-red'],'assigned'=>['Ditugaskan','badge-gray']];
                        @endphp
                        @forelse($bookings as $b)
                        @php
                            if (is_array($b)) {
                                $bId = $b['id'] ?? '';
                                $bCode = $b['code'] ?? ($b['booking_code'] ?? '');
                                $bCustomerName = is_array($b['customer'] ?? null) ? ($b['customer']['name'] ?? '-') : ($b['customer'] ?? '-');
                                $bVehicle = $b['vehicle'] ?? ($b['vehicle_name'] ?? '-');
                                $bVehicleType = in_array(strtolower($b['vehicle_type'] ?? ''), ['roda_2', 'motor']) || ($b['vehicle_type'] ?? '') === 'Roda 2' ? 'Motor' : 'Mobil';
                                $bScheduledAt = $b['scheduled_at'] ?? '-';
                                $bService = $b['service'] ?? (($b['service_type'] ?? '') === 'home' ? 'Home Service' : 'Outlet');
                                $bStatus = $b['status'] ?? 'pending';
                            } else {
                                $bId = $b->id;
                                $bCode = $b->booking_code;
                                $bCustomerName = $b->customer ? $b->customer->name : '-';
                                $bVehicle = $b->vehicle_name ?: ($b->vehicle ? $b->vehicle->brand . ' ' . $b->vehicle->model : '-');
                                $bVehicleType = in_array($b->vehicle_type, ['roda_2', 'motor']) ? 'Motor' : 'Mobil';
                                $bScheduledAt = $b->scheduled_at ? $b->scheduled_at->format('d M Y, H:i') : '-';
                                $bService = $b->service_type === 'home' ? 'Home Service' : 'Outlet';
                                $bStatus = $b->status;
                            }
                        @endphp
                        <tr class="table-row transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs font-semibold text-slate-700">{{ $bCode }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                         style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                                        {{ strtoupper(substr($bCustomerName,0,1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700 whitespace-nowrap">{{ $bCustomerName }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm text-slate-700 whitespace-nowrap">{{ $bVehicle }}</p>
                                    <p class="text-xs text-slate-400">{{ $bVehicleType }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-xs text-slate-600 whitespace-nowrap">{{ $bScheduledAt }}</p>
                                <p class="text-xs text-slate-400">{{ $bService }}</p>
                            </td>
                            <td class="px-4 py-4">
                                @php [$label,$class] = $statusBadge[$bStatus] ?? ['—','badge-gray']; @endphp
                                <span class="badge {{ $class }}">{{ $label }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-booking-detail', {detail: {id: '{{ $bId }}', code: '{{ $bCode }}'}}))"
                                            class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @if($bStatus === 'pending')
                                    <form method="POST" action="/bookings/{{ $bId }}/confirm" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-green-50 text-green-500" title="Konfirmasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                    @if(!in_array($bStatus,['completed','cancelled']))
                                    <form method="POST" action="/bookings/{{ $bId }}/cancel" class="inline"
                                          onsubmit="return confirm('Batalkan booking ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600" title="Batalkan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 text-sm">Tidak ada data booking ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            @if(isset($bookings) && method_exists($bookings, 'links'))
            <div class="px-5 py-4 border-t border-slate-100">{{ $bookings->links() }}</div>
            @else
            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Menampilkan 1–8 dari 142 booking</span>
                <div class="flex gap-1">
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40" disabled>← Prev</button>
                    <button class="px-3 py-1.5 rounded-lg text-white" style="background:#1B2337;">1</button>
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">2</button>
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">3</button>
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">Next →</button>
                </div>
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-4">
            {{-- Fleet Booking Trends --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="font-semibold text-slate-800 mb-1 text-sm">Fleet Booking Trends</h3>
                <p class="text-xs text-slate-400 mb-4">Tren 7 hari terakhir</p>
                <canvas id="trendChart" height="150"></canvas>
            </div>

            {{-- Need Assistance --}}
            <div class="rounded-2xl p-5 text-white" style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(240,196,25,0.2);">
                        <svg class="w-4 h-4" style="color:#F0C419;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-sm">Need Assistance?</h3>
                </div>
                <p class="text-xs mb-4" style="color:#94A3B8;">Contact our emergency technician center for urgent booking assistance.</p>
                <button class="w-full py-2.5 rounded-xl text-sm font-semibold text-slate-800" style="background:#F0C419;">
                    Chat Support →
                </button>
                <a href="/bookings" class="block text-center mt-2 text-xs" style="color:#7C8DB5;">View All Bookings</a>
            </div>

            {{-- Booking type breakdown --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="font-semibold text-slate-800 mb-4 text-sm">Breakdown Layanan</h3>
                <div class="space-y-3">
                    @php
                    $types = [['Home Service','65%','#F0C419'],['Outlet','35%','#3B82F6']];
                    @endphp
                    @foreach($types as [$label,$pct,$color])
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-slate-800">{{ $pct }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full">
                            <div class="h-full rounded-full" style="width:{{ $pct }}; background:{{ $color }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══ DETAIL MODAL ══ --}}
    <div x-show="showDetail" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4"
         @click.self="showDetail=false">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Detail Booking <span x-text="selectedCode" class="text-slate-400 font-normal text-sm"></span></h3>
                <button @click="showDetail=false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 text-sm text-slate-600 space-y-3">
                <p>Silakan buka halaman detail untuk informasi lengkap booking ini.</p>
                <a :href="'/bookings/'+selectedId" class="block w-full text-center py-2.5 rounded-xl text-white font-medium" style="background:#1B2337;">
                    Buka Detail Lengkap →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bookingPage() {
    return {
        showDetail: false, selectedId: '', selectedCode: '',
        openDetail(id, code) { this.selectedId = id; this.selectedCode = code; this.showDetail = true; },
        init() {
            setInterval(async () => {
                try {
                    const response = await fetch(window.location.href);
                    if (!response.ok) return;
                    const html = await response.text();
                    
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Replace stats cards
                    const newStats = doc.getElementById('stats-cards-container');
                    const oldStats = document.getElementById('stats-cards-container');
                    if (newStats && oldStats) {
                        oldStats.innerHTML = newStats.innerHTML;
                    }
                    
                    // Replace table container
                    const newTable = doc.getElementById('booking-table-container');
                    const oldTable = document.getElementById('booking-table-container');
                    if (newTable && oldTable) {
                        oldTable.innerHTML = newTable.innerHTML;
                    }
                } catch (err) {
                    console.error('Error polling bookings:', err);
                }
            }, 4000);
        }
    }
}
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
        datasets: [{
            data: [18,24,19,28,32,41,38],
            borderColor: '#F0C419', backgroundColor: 'rgba(240,196,25,0.08)',
            borderWidth: 2.5, tension: 0.4, fill: true,
            pointBackgroundColor: '#F0C419', pointRadius: 4, pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } },
            x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    }
});
</script>
@endpush
