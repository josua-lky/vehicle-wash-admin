@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="p-6 space-y-6">

    {{-- ── PAGE HEADER ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Executive Summary</h1>
            <p class="text-sm text-slate-500 mt-0.5">Real-time performance metrics untuk Vehicle Wash logistics.</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white">
                <option>Tahun 2024</option>
                <option>Tahun 2023</option>
            </select>
            <button class="flex items-center gap-2 text-sm font-medium text-white px-4 py-2 rounded-lg" style="background:#F0C419; color:#1B2337;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Card 1: Bookings Hari Ini --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bookings Hari Ini</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['bookings_today'] ?? 142 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:#FEF9EC;">
                    <svg class="w-5 h-5" style="color:#F0C419;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">+12.5%</span>
                <span class="text-xs text-slate-400">dari kemarin</span>
            </div>
        </div>

        {{-- Card 2: Clients Active --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Clients Active</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['active_clients'] ?? 38 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-400"></span> 24 online</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300"></span> 14 offline</span>
            </div>
        </div>

        {{-- Card 3: Total Revenue --}}
        <div class="stat-card rounded-2xl p-5 shadow-sm" style="background:linear-gradient(135deg,#1B2337,#252D41);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#7C8DB5;">Total Revenue</p>
                    <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($stats['total_revenue'] ?? 12800000, 0, ',', '.') }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(240,196,25,0.2);">
                    <svg class="w-5 h-5" style="color:#F0C419;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(240,196,25,0.2); color:#F0C419;">+8.3%</span>
                <span class="text-xs" style="color:#7C8DB5;">bulan ini</span>
            </div>
        </div>

        {{-- Card 4: Teknisi Aktif --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Teknisi Aktif</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['active_technicians'] ?? 24 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-purple-50">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1">
                <div class="h-1.5 rounded-full" style="width:68%; background:#A78BFA;"></div>
            </div>
            <p class="text-xs text-slate-400 mt-1.5">68% utilisasi kapasitas</p>
        </div>
    </div>

    {{-- ── CHART + BOOKINGS TABLE ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

        {{-- Bar Chart --}}
        <div class="xl:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-semibold text-slate-800">Tren Transaksi Bulanan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Jumlah booking per bulan</p>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-xs px-3 py-1.5 rounded-lg font-medium text-white" style="background:#F0C419; color:#1B2337;">Bulanan</button>
                    <button class="text-xs px-3 py-1.5 rounded-lg font-medium text-slate-500 hover:bg-slate-50">Mingguan</button>
                </div>
            </div>
            <canvas id="transactionChart" height="200"></canvas>
        </div>

        {{-- Bookings Terbaru --}}
        <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-slate-800">Bookings Terbaru</h3>
                <a href="/bookings" class="text-xs font-medium" style="color:#F0C419;">Lihat Semua →</a>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto">
                @php
                $recentBookings = $recentBookings ?? [
                    ['id'=>'VW-2024-0841','customer'=>'Budi Santoso','vehicle'=>'Toyota Avanza','status'=>'completed','time'=>'13 Nov, 09:00'],
                    ['id'=>'VW-2024-0840','customer'=>'Siti Rahayu','vehicle'=>'Honda Beat','status'=>'in_progress','time'=>'13 Nov, 08:30'],
                    ['id'=>'VW-2024-0839','customer'=>'Andi Wijaya','vehicle'=>'Yamaha NMAX','status'=>'pending','time'=>'13 Nov, 07:45'],
                    ['id'=>'VW-2024-0838','customer'=>'Dewi Kusuma','vehicle'=>'Honda CRV','status'=>'confirmed','time'=>'12 Nov, 16:00'],
                    ['id'=>'VW-2024-0837','customer'=>'Reza Pratama','vehicle'=>'Mitsubishi Pajero','status'=>'cancelled','time'=>'12 Nov, 14:20'],
                    ['id'=>'VW-2024-0836','customer'=>'Lina Marlina','vehicle'=>'Honda Vario','status'=>'completed','time'=>'12 Nov, 11:00'],
                ];
                $statusMap = [
                    'completed'=>['label'=>'Selesai','class'=>'badge-green'],
                    'in_progress'=>['label'=>'Proses','class'=>'badge-blue'],
                    'pending'=>['label'=>'Pending','class'=>'badge-yellow'],
                    'confirmed'=>['label'=>'Konfirmasi','class'=>'badge-purple'],
                    'cancelled'=>['label'=>'Batal','class'=>'badge-red'],
                ];
                @endphp
                @foreach($recentBookings as $booking)
                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                        {{ strtoupper(substr($booking['customer'],0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 truncate">{{ $booking['customer'] }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $booking['vehicle'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="badge {{ $statusMap[$booking['status']]['class'] }}">{{ $statusMap[$booking['status']]['label'] }}</span>
                        <p class="text-xs text-slate-400 mt-1">{{ $booking['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── BOTTOM ROW: Map + Quick Stats ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Vehicle Coverage Map --}}
        <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-slate-800">Vehicle Coverage Map</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Sebaran lokasi teknisi aktif saat ini</p>
                </div>
                <a href="/technicians" class="text-sm font-medium px-4 py-2 rounded-lg text-white" style="background:#1B2337;">
                    Atur Live Now
                </a>
            </div>
            {{-- Map placeholder (ganti dengan Google Maps Embed atau Leaflet.js) --}}
            <div class="relative rounded-xl overflow-hidden" style="height:220px; background:linear-gradient(135deg,#1B2337 0%,#252D41 100%);">
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(rgba(240,196,25,0.3) 1px, transparent 1px); background-size:30px 30px;"></div>
                {{-- Fake map pins --}}
                @foreach([['15%','30%'],['40%','50%'],['65%','25%'],['75%','60%'],['30%','70%'],['55%','40%']] as $i=>$pos)
                <div class="absolute flex items-center justify-center" style="left:{{ $pos[0] }}; top:{{ $pos[1] }}; transform:translate(-50%,-50%)">
                    <div class="w-3 h-3 rounded-full animate-pulse" style="background:#F0C419;"></div>
                    <div class="absolute w-6 h-6 rounded-full opacity-30" style="background:#F0C419;"></div>
                </div>
                @endforeach
                <div class="absolute bottom-4 left-4 text-xs text-white opacity-60">● {{ $stats['active_technicians'] ?? 24 }} Teknisi Online</div>
            </div>
        </div>

        {{-- Quick Performance --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-5">Performa Hari Ini</h3>
            <div class="space-y-4">
                @php
                $perfs = [
                    ['label'=>'Selesai','value'=>89,'total'=>142,'color'=>'#10B981'],
                    ['label'=>'Dalam Proses','value'=>31,'total'=>142,'color'=>'#3B82F6'],
                    ['label'=>'Pending','value'=>15,'total'=>142,'color'=>'#F59E0B'],
                    ['label'=>'Dibatalkan','value'=>7,'total'=>142,'color'=>'#EF4444'],
                ];
                @endphp
                @foreach($perfs as $p)
                @php $pct = round($p['value']/$p['total']*100); @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-slate-600 font-medium">{{ $p['label'] }}</span>
                        <span class="font-bold text-slate-800">{{ $p['value'] }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700" style="width:{{ $pct }}%; background:{{ $p['color'] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-2">Rating Rata-rata Hari Ini</p>
                <div class="flex items-center gap-2">
                    <div class="flex">
                        @for($i=1;$i<=5;$i++)
                        <svg class="w-4 h-4 {{ $i<=4 ? '' : 'opacity-30' }}" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <span class="text-sm font-bold text-slate-800">4.82</span>
                    <span class="text-xs text-slate-400">/ 5.0</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('transactionChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Total Booking',
            data: [85, 92, 108, 125, 98, 134, 145, 138, 122, 156, 142, 0],
            backgroundColor: (ctx) => {
                const i = ctx.dataIndex;
                return i === 10 ? '#F0C419' : 'rgba(240,196,25,0.25)';
            },
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false }, tooltip: {
            backgroundColor: '#1B2337', titleColor: '#fff', bodyColor: '#94A3B8',
            padding: 10, cornerRadius: 8,
            callbacks: { label: (c) => ` ${c.parsed.y} booking` }
        }},
        scales: {
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false },
                 ticks: { color: '#94A3B8', font: { size: 11 } } },
            x: { grid: { display: false }, border: { display: false },
                 ticks: { color: '#94A3B8', font: { size: 11 } } }
        }
    }
});
</script>
@endpush
