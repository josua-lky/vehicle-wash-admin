@extends('layouts.app')
@section('title', 'Laporan & Analitik')

@section('content')
<div class="p-6 space-y-5">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Reports & Analytics</h1>
            <p class="text-sm text-slate-500 mt-0.5">Analisis performa dan pendapatan layanan Vehicle Wash</p>
        </div>
        <form method="GET" action="/reports" class="flex gap-2" id="filterForm">
            <select name="outlet_id" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white">
                <option value="">Semua Outlet</option>
                @foreach($outlets ?? [] as $o)
                <option value="{{ is_array($o) ? $o['id'] : $o->id }}" {{ request('outlet_id') == (is_array($o) ? $o['id'] : $o->id) ? 'selected' : '' }}>
                    {{ is_array($o) ? $o['name'] : $o->name }}
                </option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <input type="date" name="date_from" value="{{ $from }}" onchange="this.form.submit()" class="text-sm text-slate-600 bg-transparent">
                <span class="text-slate-300">—</span>
                <input type="date" name="date_to" value="{{ $to }}" onchange="this.form.submit()" class="text-sm text-slate-600 bg-transparent">
            </div>
            <a href="/reports/export?format=excel&outlet_id={{ request('outlet_id') }}&date_from={{ $from }}&date_to={{ $to }}" class="flex items-center gap-1.5 text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#10B981;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel
            </a>
            <a href="/reports/export?format=pdf&outlet_id={{ request('outlet_id') }}&date_from={{ $from }}&date_to={{ $to }}" class="flex items-center gap-1.5 text-sm font-semibold px-4 py-2 rounded-lg text-slate-900" style="background:#F0C419;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </form>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $rStats = [
            ['label'=>'Monthly Revenue','value'=>$stats['monthly_revenue']['value'],'change'=>$stats['monthly_revenue']['change'],'up'=>$stats['monthly_revenue']['up'],'sub'=>$stats['monthly_revenue']['sub']],
            ['label'=>'Orders Served','value'=>$stats['orders_served']['value'],'change'=>$stats['orders_served']['change'],'up'=>$stats['orders_served']['up'],'sub'=>$stats['orders_served']['sub']],
            ['label'=>'Avg. Per Order','value'=>$stats['avg_per_order']['value'],'change'=>$stats['avg_per_order']['change'],'up'=>$stats['avg_per_order']['up'],'sub'=>$stats['avg_per_order']['sub']],
            ['label'=>'Satisfaction','value'=>$stats['satisfaction']['value'],'change'=>$stats['satisfaction']['change'],'up'=>$stats['satisfaction']['up'],'sub'=>$stats['satisfaction']['sub']],
        ];
        @endphp
        @foreach($rStats as $s)
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ $s['label'] }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1 mb-2">{{ $s['value'] }}</p>
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $s['up']?'bg-green-50 text-green-600':'bg-red-50 text-red-600' }}">{{ $s['change'] }}</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- CHARTS ROW --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Revenue & Performance Trends --}}
        <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-slate-800">Revenue & Performance Trends</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pendapatan & volume order 12 bulan terakhir</p>
                </div>
                <div class="flex gap-2 text-xs">
                    <button id="btn-revenue" class="px-3 py-1.5 rounded-lg font-medium text-white transition-all duration-200" style="background:#1B2337;">Revenue</button>
                    <button id="btn-volume" class="px-3 py-1.5 rounded-lg font-medium text-slate-500 border border-slate-200 hover:bg-slate-50 transition-all duration-200">Volume</button>
                </div>
            </div>
            <div class="relative w-full" style="height: 320px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Service Type Distribution --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-1">Service Type Distribution</h3>
            <p class="text-xs text-slate-400 mb-5">Persentase berdasarkan jenis layanan</p>
            <div class="space-y-4">
                @foreach($serviceDistribution as $sd)
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-slate-600">{{ $sd['label'] }}</span>
                        <span class="font-semibold text-slate-800">{{ $sd['pct'] }}</span>
                    </div>
                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700" style="width:{{ $sd['pct'] }}; background:{{ $sd['color'] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="relative w-full mt-6" style="height: 220px;">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    {{-- SECOND CHARTS ROW --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Outlet Performance --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-5">Performa Per Outlet</h3>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="outletChart"></canvas>
            </div>
        </div>

        {{-- Technician Leaderboard --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-slate-800">Top Teknisi Bulan Ini</h3>
                <a href="/technicians" class="text-xs" style="color:#F0C419;">Lihat Semua →</a>
            </div>
            <div class="space-y-3">
                @foreach($leaderboard as $t)
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50">
                    <span class="text-lg w-6 text-center">{{ $t['medal'] }}</span>
                    <img src="{{ $t['avatar'] }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $t['name'] }}">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-700">{{ $t['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $t['orders'] }} order selesai</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span class="text-sm font-bold text-slate-700">{{ $t['rating'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RECENT TRANSACTION TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Recent Transaction Reports</h3>
            <a href="/payments" class="text-xs font-medium" style="color:#F0C419;">View All Reports →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">#Transaction</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Vehicle Type</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Service</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                    $months_id = ['Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ags', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'];
                    @endphp
                    @forelse($recentPayments as $p)
                    @php
                        $payBookingCode = $p->booking ? $p->booking->booking_code : '-';
                        $dt = $p->paid_at ?? $p->created_at;
                        $dateStr = $dt ? $dt->format('d') . ' ' . ($months_id[$dt->format('M')] ?? $dt->format('M')) . ' ' . $dt->format('Y') : '-';
                        $vehicleName = $p->booking ? $p->booking->vehicle_name : '-';
                        $packageName = $p->booking && $p->booking->package ? $p->booking->package->name : ($p->booking->service_type === 'home' ? 'Home Detailing' : 'Standard Wash');
                        
                        $payStatusBadge=['paid'=>['Sukses','badge-green'],'pending'=>['Pending','badge-yellow'],'failed'=>['Gagal','badge-red'],'refunded'=>['Refund','badge-purple'],'expired'=>['Expired','badge-gray']];
                        [$slabel,$sclass] = $payStatusBadge[$p->status] ?? ['—','badge-gray'];
                    @endphp
                    <tr class="table-row">
                        <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-700">{{ $payBookingCode }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-600">{{ $dateStr }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-700">{{ $vehicleName }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-600">{{ $packageName }}</td>
                        <td class="px-4 py-3.5">
                            <span class="badge {{ $sclass }}">{{ $slabel }}</span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-800 text-xs">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400 text-sm">
                            Tidak ada transaksi dalam periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue trend chart
const chartData = {
    revenue: {
        label: 'Revenue (jt)',
        data: {!! json_encode($revenueData) !!},
        target: {!! json_encode($targetRevenue) !!},
        targetLabel: 'Target Revenue (jt)'
    },
    volume: {
        label: 'Volume (order)',
        data: {!! json_encode($volumeData) !!},
        target: {!! json_encode($targetVolume) !!},
        targetLabel: 'Target Volume (order)'
    }
};

const revenueChart = new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            { type: 'line', label: 'Target', data: chartData.revenue.target, borderColor: 'rgba(240,196,25,0.4)', borderWidth: 1.5, borderDash: [5,5], pointRadius: 0, fill: false, tension: 0 },
            { type: 'bar', label: 'Revenue (jt)', data: chartData.revenue.data, backgroundColor: (ctx) => ctx.dataIndex === 11 ? '#F0C419' : 'rgba(240,196,25,0.2)', borderRadius: 5, borderSkipped: false }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1B2337', titleColor: '#fff', bodyColor: '#94A3B8', cornerRadius: 8, padding: 10 } },
        scales: {
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false }, ticks: { color: '#94A3B8', font: { size: 11 } } },
            x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94A3B8', font: { size: 11 } } }
        }
    }
});

// Toggling logic for Revenue/Volume chart
const revBtn = document.getElementById('btn-revenue');
const volBtn = document.getElementById('btn-volume');

function updateChart(type) {
    const info = chartData[type];
    revenueChart.data.datasets[0].data = info.target;
    revenueChart.data.datasets[0].label = info.targetLabel;
    revenueChart.data.datasets[1].data = info.data;
    revenueChart.data.datasets[1].label = info.label;
    
    revenueChart.data.datasets[1].backgroundColor = (ctx) => ctx.dataIndex === 11 ? '#F0C419' : 'rgba(240,196,25,0.2)';
    revenueChart.update();
    
    if (type === 'revenue') {
        revBtn.style.background = '#1B2337';
        revBtn.style.color = '#fff';
        volBtn.style.background = 'transparent';
        volBtn.style.color = '#64748B';
        volBtn.classList.add('border', 'border-slate-200');
        revBtn.classList.remove('border', 'border-slate-200');
    } else {
        volBtn.style.background = '#1B2337';
        volBtn.style.color = '#fff';
        revBtn.style.background = 'transparent';
        revBtn.style.color = '#64748B';
        revBtn.classList.add('border', 'border-slate-200');
        volBtn.classList.remove('border', 'border-slate-200');
    }
}

revBtn.addEventListener('click', () => updateChart('revenue'));
volBtn.addEventListener('click', () => updateChart('volume'));

// Donut chart for Service Type Distribution
new Chart(document.getElementById('donutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_column($serviceDistribution, 'label')) !!},
        datasets: [{ 
            data: {!! json_encode(array_column($serviceDistribution, 'pct_val')) !!}, 
            backgroundColor: {!! json_encode(array_column($serviceDistribution, 'color')) !!}, 
            borderWidth: 0, 
            hoverOffset: 4 
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '70%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, color: '#64748B', boxWidth: 10, padding: 8 } } }
    }
});

// Outlet performance chart
new Chart(document.getElementById('outletChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($outletPerformance, 'name')) !!},
        datasets: [
            { label: 'Pendapatan (jt)', data: {!! json_encode(array_column($outletPerformance, 'revenue_jt')) !!}, backgroundColor: '#F0C419', borderRadius: 5 },
            { label: 'Order', data: {!! json_encode(array_column($outletPerformance, 'orders')) !!}, backgroundColor: 'rgba(59,130,246,0.7)', borderRadius: 5 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, color: '#64748B', boxWidth: 10, padding: 8 } } },
        scales: {
            x: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } },
            y: { grid: { display: false }, border: { display: false }, ticks: { color: '#64748B', font: { size: 11 } } }
        }
    }
});
</script>
@endpush
