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
        <div class="flex gap-2">
            <select class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-white">
                <option>Semua Outlet</option>
                @foreach($outlets ?? [] as $o)
                <option>{{ is_array($o) ? $o['name'] : $o->name }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <input type="date" value="{{ date('Y-m-01') }}" class="text-sm text-slate-600 bg-transparent">
                <span class="text-slate-300">—</span>
                <input type="date" value="{{ date('Y-m-d') }}" class="text-sm text-slate-600 bg-transparent">
            </div>
            <a href="/reports/export?format=excel" class="flex items-center gap-1.5 text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#10B981;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel
            </a>
            <a href="/reports/export?format=pdf" class="flex items-center gap-1.5 text-sm font-semibold px-4 py-2 rounded-lg text-slate-900" style="background:#F0C419;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $rStats = [
            ['label'=>'Monthly Revenue','value'=>'Rp 42,850,000','change'=>'+5.2%','up'=>true,'sub'=>'Rp 40,700,000 bulan lalu'],
            ['label'=>'Orders Served','value'=>'1,294','change'=>'+8.1%','up'=>true,'sub'=>'24,533 rata-rata bulanan'],
            ['label'=>'Avg. Per Order','value'=>'Rp 33,120','change'=>'-2.3%','up'=>false,'sub'=>'vs Rp 33,890 bulan lalu'],
            ['label'=>'Satisfaction','value'=>'98.2%','change'=>'+0.4%','up'=>true,'sub'=>'dari 1,204 ulasan'],
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
                    <button class="px-3 py-1.5 rounded-lg font-medium text-white" style="background:#1B2337;">Revenue</button>
                    <button class="px-3 py-1.5 rounded-lg font-medium text-slate-500 border border-slate-200 hover:bg-slate-50">Volume</button>
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
                @php
                $services=[['Premium Full Wash','40%','#1B2337'],['Standard Exterior','33%','#F0C419'],['Home Detailing','16%','#3B82F6'],['LS Priority Club','5%','#10B981'],['Lainnya','6%','#94A3B8']];
                @endphp
                @foreach($services as [$label,$pct,$color])
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-slate-600">{{ $label }}</span>
                        <span class="font-semibold text-slate-800">{{ $pct }}</span>
                    </div>
                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700" style="width:{{ $pct }}; background:{{ $color }};"></div>
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
                @foreach([['Ahmad Fauzi',248,'4.9','#F0C419','🥇'],['Citra Putri',312,'4.8','#94A3B8','🥈'],['Budi Santoso',185,'4.7','#CD7F32','🥉'],['Eko Prasetyo',198,'4.6','#64748B','4'],['Fitri Handayani',143,'4.5','#64748B','5']] as [$name,$orders,$rating,$color,$medal])
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50">
                    <span class="text-lg">{{ $medal }}</span>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:linear-gradient(135deg,#1B2337,#3B82F6);">{{ $name[0] }}</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-700">{{ $name }}</p>
                        <p class="text-xs text-slate-400">{{ $orders }} order selesai</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span class="text-sm font-bold text-slate-700">{{ $rating }}</span>
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
                    $recentTxn=[
                        ['id'=>'#CV-2024-8481','date'=>'13 Okt 2024','vehicle'=>'Toyota Model X','service'=>'Premium Full Wash','status'=>'active','amount'=>'Rp 55,000'],
                        ['id'=>'#CV-2024-8480','date'=>'12 Okt 2024','vehicle'=>'Honda Beat','service'=>'Standard Exterior','status'=>'active','amount'=>'Rp 25,000'],
                        ['id'=>'#CV-2024-8479','date'=>'12 Okt 2024','vehicle'=>'Free D40 Pro','service'=>'Home Detailing','status'=>'inactive','amount'=>'Rp 150,000'],
                        ['id'=>'#CV-2024-8478','date'=>'12 Okt 2024','vehicle'=>'JRN Arc 30','service'=>'LS Priority Club','status'=>'active','amount'=>'Rp 380,000'],
                        ['id'=>'#CV-2024-8477','date'=>'11 Okt 2024','vehicle'=>'Toyota Avanza','service'=>'Standard Exterior','status'=>'active','amount'=>'Rp 35,000'],
                    ];
                    @endphp
                    @foreach($recentTxn as $t)
                    <tr class="table-row">
                        <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-700">{{ $t['id'] }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-600">{{ $t['date'] }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-700">{{ $t['vehicle'] }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-600">{{ $t['service'] }}</td>
                        <td class="px-4 py-3.5">
                            <span class="badge {{ $t['status']==='active'?'badge-green':'badge-red' }}">{{ $t['status']==='active'?'Aktif':'Nonaktif' }}</span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-800 text-xs">{{ $t['amount'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue trend chart
new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'],
        datasets: [
            { type: 'line', label: 'Target', data: [35,35,38,38,42,42,44,44,46,46,48,48], borderColor: 'rgba(240,196,25,0.4)', borderWidth: 1.5, borderDash: [5,5], pointRadius: 0, fill: false, tension: 0 },
            { type: 'bar', label: 'Revenue (jt)', data: [28,32,35,30,38,42,45,39,43,48,43,0], backgroundColor: (ctx) => ctx.dataIndex === 9 ? '#F0C419' : 'rgba(240,196,25,0.2)', borderRadius: 5, borderSkipped: false }
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

// Donut chart
new Chart(document.getElementById('donutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Premium Full Wash','Standard Exterior','Home Detailing','LS Priority','Lainnya'],
        datasets: [{ data: [40,33,16,5,6], backgroundColor: ['#1B2337','#F0C419','#3B82F6','#10B981','#94A3B8'], borderWidth: 0, hoverOffset: 4 }]
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
        labels: ['Outlet Pusat','Outlet Bekasi','Outlet Tangerang','Outlet Depok','Outlet Bogor'],
        datasets: [
            { label: 'Pendapatan (jt)', data: [18.5, 12.3, 9.8, 7.2, 5.1], backgroundColor: '#F0C419', borderRadius: 5 },
            { label: 'Order', data: [420, 310, 248, 186, 130], backgroundColor: 'rgba(59,130,246,0.7)', borderRadius: 5 }
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
