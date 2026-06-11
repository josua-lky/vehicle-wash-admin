@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="p-6 space-y-6" x-data="dashboardPage()">

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
                @php $isPos = ($stats['bookings_today_pct'] ?? 0) >= 0; @endphp
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $isPos ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                    {{ $isPos ? '+' : '' }}{{ $stats['bookings_today_pct'] ?? 0 }}%
                </span>
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
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-400"></span> {{ $stats['active_clients'] ?? 0 }} online</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300"></span> {{ $stats['inactive_clients'] ?? 0 }} offline</span>
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
                @php $isRevPos = ($stats['revenue_change_pct'] ?? 0) >= 0; @endphp
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:{{ $isRevPos ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; color:{{ $isRevPos ? '#10B981' : '#EF4444' }};">
                    {{ $isRevPos ? '+' : '' }}{{ $stats['revenue_change_pct'] ?? 0 }}%
                </span>
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
                <div class="h-1.5 rounded-full" style="width:{{ $stats['tech_utilization'] ?? 0 }}%; background:#A78BFA;"></div>
            </div>
            <p class="text-xs text-slate-400 mt-1.5">{{ $stats['tech_utilization'] ?? 0 }}% utilisasi kapasitas</p>
        </div>
    </div>

    {{-- ── CHART + BOOKINGS TABLE ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

        {{-- Bar Chart --}}
        <div class="xl:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-semibold text-slate-800" x-text="activeTrend === 'monthly' ? 'Tren Transaksi Bulanan' : 'Tren Transaksi Mingguan'">Tren Transaksi Bulanan</h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="activeTrend === 'monthly' ? 'Jumlah booking per bulan' : 'Jumlah booking per hari (minggu ini)'">Jumlah booking per bulan</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="switchTrend('monthly')"
                            :style="activeTrend === 'monthly' ? 'background:#F0C419; color:#1B2337;' : ''"
                            :class="activeTrend === 'monthly' ? 'text-white' : 'text-slate-500 hover:bg-slate-50'"
                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors">Bulanan</button>
                    <button @click="switchTrend('weekly')"
                            :style="activeTrend === 'weekly' ? 'background:#F0C419; color:#1B2337;' : ''"
                            :class="activeTrend === 'weekly' ? 'text-white' : 'text-slate-500 hover:bg-slate-50'"
                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors">Mingguan</button>
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

    {{-- ── BOTTOM ROW: Quick Stats ── --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Progress Bars --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-slate-800 mb-4">Performa Hari Ini</h3>
                @php
                $perfs = [
                    ['label'=>'Selesai','value'=>$stats['perf_completed'] ?? 0,'total'=>$stats['perf_total'] ?? 0,'color'=>'#10B981'],
                    ['label'=>'Dalam Proses','value'=>$stats['perf_in_progress'] ?? 0,'total'=>$stats['perf_total'] ?? 0,'color'=>'#3B82F6'],
                    ['label'=>'Pending','value'=>$stats['perf_pending'] ?? 0,'total'=>$stats['perf_total'] ?? 0,'color'=>'#F59E0B'],
                    ['label'=>'Dibatalkan','value'=>$stats['perf_cancelled'] ?? 0,'total'=>$stats['perf_total'] ?? 0,'color'=>'#EF4444'],
                ];
                @endphp
                @foreach($perfs as $p)
                @php $pct = $p['total'] > 0 ? round($p['value']/$p['total']*100) : 0; @endphp
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

            {{-- Ratings Card --}}
            <div class="flex flex-col justify-center items-center p-6 bg-slate-50/50 rounded-2xl border border-slate-100 text-center">
                <p class="text-sm font-semibold text-slate-500 mb-2">Rating Rata-rata Hari Ini</p>
                <div class="flex items-baseline gap-1.5 mb-3">
                    <span class="text-4xl font-extrabold text-slate-800">4.8</span>
                    <span class="text-sm text-slate-400">/ 5.0</span>
                </div>
                <div class="flex gap-1 mb-2">
                    @for($i=1;$i<=5;$i++)
                    <svg class="w-5 h-5 {{ $i<=4 ? '' : 'opacity-30' }}" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
                <p class="text-xs text-slate-400">Berdasarkan ulasan pelanggan hari ini</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const monthlyData = @json($monthlyData);
const weeklyData = @json($weeklyData);
const monthlyLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
const weeklyLabels = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];

function dashboardPage() {
    return {
        activeTrend: 'monthly',
        init() {
            window.dashboardApp = this;
        },
        switchTrend(type) {
            this.activeTrend = type;
            if (type === 'monthly') {
                window.transactionChart.data.labels = monthlyLabels;
                window.transactionChart.data.datasets[0].data = monthlyData;
            } else {
                window.transactionChart.data.labels = weeklyLabels;
                window.transactionChart.data.datasets[0].data = weeklyData;
            }
            window.transactionChart.update();
        }
    }
}

const ctx = document.getElementById('transactionChart').getContext('2d');
window.transactionChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Total Booking',
            data: monthlyData,
            backgroundColor: (context) => {
                const i = context.dataIndex;
                const activeTrend = window.dashboardApp ? window.dashboardApp.activeTrend : 'monthly';
                const activeIndex = activeTrend === 'monthly' ? new Date().getMonth() : (new Date().getDay() + 6) % 7;
                return i === activeIndex ? '#F0C419' : 'rgba(240,196,25,0.25)';
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
