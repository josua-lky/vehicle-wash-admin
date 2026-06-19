@extends('layouts.app')
@section('title', 'Manajemen Pembayaran')

@section('content')
<div class="p-6 space-y-5">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Pembayaran</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitor seluruh transaksi pembayaran layanan</p>
        </div>
        <div class="flex gap-2">
            <a href="/payments/export?format=excel&{{ http_build_query(request()->except('format')) }}" class="flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg text-white" style="background:#10B981;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel
            </a>
            <a href="/payments/export?format=pdf&{{ http_build_query(request()->except('format')) }}" target="_blank" class="flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg text-white" style="background:#1B2337;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- TOP CARDS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Revenue Overview --}}
        <div class="lg:col-span-2 rounded-2xl p-6 shadow-sm text-white" style="background:linear-gradient(135deg,#1B2337 0%,#2D3D5E 100%);">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#7C8DB5;">Revenue Overview</p>
                    <p class="text-xs mb-3" style="color:#7C8DB5;">Total pendapatan untuk siklus penagihan saat ini</p>
                    <p class="text-4xl font-black">Rp {{ number_format($stats['total_revenue'] ?? 45280000, 0, ',', '.') }}</p>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background:rgba(16,185,129,0.2);">
                    <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span class="text-xs text-green-400 font-semibold">+12.5%</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                @php
                $revStats = [
                    ['label'=>'Sukses','value'=>'Rp '.number_format($stats['paid'] ?? 0, 0, ',', '.'),'color'=>'#10B981'],
                    ['label'=>'Pending','value'=>'Rp '.number_format($stats['pending'] ?? 0, 0, ',', '.'),'color'=>'#F0C419'],
                    ['label'=>'Refund','value'=>'Rp '.number_format($stats['refunded'] ?? 0, 0, ',', '.'),'color'=>'#EF4444']
                ];
                @endphp
                @foreach($revStats as $rs)
                <div class="p-3 rounded-xl" style="background:rgba(255,255,255,0.07);">
                    <p class="text-xs font-medium mb-1" style="color:#94A3B8;">{{ $rs['label'] }}</p>
                    <p class="text-sm font-bold" style="color:{{ $rs['color'] }};">{{ $rs['value'] }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-5">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>

        {{-- Quick Payouts --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#FEF9EC;">
                        <svg class="w-5 h-5" style="color:#F0C419;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-800 text-sm">Quick Payouts</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">Proses pembayaran teknisi yang pending segera untuk menjaga kepercayaan tim.</p>
                <div class="space-y-2 mb-4">
                    @forelse($payouts ?? [] as $p)
                    <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white overflow-hidden flex-shrink-0" style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                                @if($p['avatar'])
                                <img src="{{ $p['avatar'] }}" alt="avatar" class="w-full h-full object-cover">
                                @else
                                {{ substr($p['name'], 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-700">{{ $p['name'] }}</p>
                                <p class="text-xs text-slate-400">{{ $p['orders_count'] }} order selesai</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold" style="color:#F0C419;">Rp {{ number_format($p['payout_amount'], 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-4">Tidak ada payout tertunda.</p>
                    @endforelse
                </div>
                @if(count($payouts ?? []) > 0)
                <form action="/payments/process-payouts" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold text-slate-900 active:scale-95 transition-all" style="background:#F0C419;">
                        Process All Payouts
                    </button>
                </form>
                @else
                <button disabled class="w-full py-2.5 rounded-xl text-sm font-semibold text-slate-400 bg-slate-100 cursor-not-allowed">
                    No Payouts to Process
                </button>
                @endif
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase mb-3">Metode Pembayaran</p>
                @foreach($methodBreakdown ?? [] as $mb)
                <div class="flex items-center gap-3 mb-2.5">
                    <span class="text-xs text-slate-600 w-28 truncate">{{ $mb['label'] }}</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full"><div class="h-full rounded-full" style="width:{{ $mb['pct'] }}; background:{{ $mb['color'] }};"></div></div>
                    <span class="text-xs font-semibold text-slate-700 w-8 text-right">{{ $mb['pct'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="/payments" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-48">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID transaksi, nama..." class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-600 bg-slate-50">
        </div>
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
            <option value="">Semua Status</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sukses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refund</option>
        </select>
        <select name="method" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 bg-slate-50">
            <option value="">Semua Metode</option>
            <option value="ewallet" {{ request('method') === 'ewallet' ? 'selected' : '' }}>OnoPay</option>
        </select>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
            <input type="date" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}" onchange="this.form.submit()" class="text-sm text-slate-600 bg-transparent">
            <span class="text-slate-300 text-xs">—</span>
            <input type="date" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" onchange="this.form.submit()" class="text-sm text-slate-600 bg-transparent">
        </div>
        <button type="submit" class="text-xs font-semibold px-4 py-2 rounded-lg text-white" style="background:#1B2337;">Filter</button>
        <a href="/payments" class="text-xs font-semibold px-4 py-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 flex items-center justify-center">Reset</a>
    </form>

    {{-- TRANSACTION TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Transaction ID</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Tanggal</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nominal</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Metode</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                    $payments = $payments ?? collect([
                        ['id'=>'PAY-2024-8481','booking_id'=>'VW-2024-0841','customer'=>'Budi Santoso','date'=>'13 Okt 2024','amount'=>55000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'paid'],
                        ['id'=>'PAY-2024-8480','booking_id'=>'VW-2024-0840','customer'=>'Siti Rahayu','date'=>'12 Okt 2024','amount'=>25000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'paid'],
                        ['id'=>'PAY-2024-8479','booking_id'=>'VW-2024-0839','customer'=>'Andi Wijaya','date'=>'12 Okt 2024','amount'=>90000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'pending'],
                        ['id'=>'PAY-2024-8478','booking_id'=>'VW-2024-0838','customer'=>'Dewi Kusuma','date'=>'12 Okt 2024','amount'=>180000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'paid'],
                        ['id'=>'PAY-2024-8477','booking_id'=>'VW-2024-0837','customer'=>'Reza Pratama','date'=>'11 Okt 2024','amount'=>55000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'refunded'],
                        ['id'=>'PAY-2024-8476','booking_id'=>'VW-2024-0836','customer'=>'Lina Marlina','date'=>'11 Okt 2024','amount'=>25000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'paid'],
                        ['id'=>'PAY-2024-8475','booking_id'=>'VW-2024-0835','customer'=>'Hendra G.','date'=>'11 Okt 2024','amount'=>40000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'paid'],
                        ['id'=>'PAY-2024-8474','booking_id'=>'VW-2024-0834','customer'=>'Maya Sari','date'=>'10 Okt 2024','amount'=>90000,'method'=>'OnoPay','method_type'=>'ewallet','status'=>'failed'],
                    ]);
                    $payStatusBadge=['paid'=>['Sukses','badge-green'],'pending'=>['Pending','badge-yellow'],'failed'=>['Gagal','badge-red'],'refunded'=>['Refund','badge-purple'],'expired'=>['Expired','badge-gray']];
                    $methodIcons=['ewallet'=>['💳','text-purple-500'],'va'=>['🏦','text-blue-500'],'qris'=>['📱','text-green-500'],'cod'=>['💵','text-amber-500']];
                    @endphp
                    @foreach($payments as $pay)
                    @php
                        if (is_array($pay)) {
                            $payId = $pay['id'] ?? '';
                            $payBookingCode = $pay['booking_id'] ?? '';
                            $payCustomerName = $pay['customer'] ?? '-';
                            $payDate = $pay['date'] ?? '-';
                            $payAmount = $pay['amount'] ?? 0;
                            $payMethod = $pay['method'] ?? 'OnoPay';
                            $payMethodType = $pay['method_type'] ?? 'ewallet';
                            $payStatus = $pay['status'] ?? 'pending';
                            $payActionId = $pay['id'] ?? '';
                        } else {
                            $payId = 'PAY-' . $pay->id;
                            $payBookingCode = $pay->booking ? $pay->booking->booking_code : '-';
                            $payCustomerName = $pay->booking && $pay->booking->customer ? $pay->booking->customer->name : '-';
                            $payDate = $pay->paid_at ? $pay->paid_at->format('d M Y') : ($pay->created_at ? $pay->created_at->format('d M Y') : '-');
                            $payAmount = $pay->amount;
                            $payMethod = 'OnoPay';
                            $payMethodType = 'ewallet';
                            $payStatus = $pay->status;
                            $payActionId = $pay->id;
                        }
                        [$slabel,$sclass] = $payStatusBadge[$payStatus] ?? ['—','badge-gray'];
                    @endphp
                    <tr class="table-row">
                        <td class="px-5 py-4">
                            <p class="font-mono text-xs font-semibold text-slate-700">{{ $payId }}</p>
                            <p class="text-xs text-slate-400">{{ $payBookingCode }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">{{ substr($payCustomerName,0,1) }}</div>
                                <span class="text-sm text-slate-700">{{ $payCustomerName }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $payDate }}</td>
                        <td class="px-4 py-4">
                            <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($payAmount,0,',','.') }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1.5">
                                <span>{{ $methodIcons[$payMethodType][0] ?? '💳' }}</span>
                                <span class="text-xs text-slate-600">{{ $payMethod }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4"><span class="badge {{ $sclass }}">{{ $slabel }}</span></td>
                        <td class="px-4 py-4">
                            <div class="flex gap-1">
                                <a href="/payments/{{ $payActionId }}" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Detail</a>
                                @if($payStatus==='paid')
                                <form method="POST" action="/payments/{{ $payActionId }}/refund" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50">Refund</button>
                                </form>
                                @elseif($payStatus==='pending')
                                <form method="POST" action="/payments/{{ $payActionId }}/confirm" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg text-white" style="background:#10B981;">Konfirmasi</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Menampilkan 1–8 dari {{ $totalPayments ?? 1294 }} transaksi</span>
            <div class="flex gap-1">
                <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40" disabled>← Prev</button>
                <button class="px-3 py-1.5 rounded-lg text-white" style="background:#1B2337;">1</button>
                <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">2</button>
                <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">Next →</button>
            </div>
        </div>
    </div>

    {{-- SECURE TRANSACTION PROTOCOL --}}
    <div class="rounded-2xl p-5 flex items-center gap-5" style="background:linear-gradient(135deg,#1B2337,#252D41);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,196,25,0.15);">
            <svg class="w-6 h-6" style="color:#F0C419;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="flex-1">
            <h3 class="text-white font-semibold text-sm">Secure Transaction Protocol</h3>
            <p class="text-xs mt-0.5" style="color:#7C8DB5;">Semua transaksi diproses secara aman menggunakan protokol OnoPay Gateway.</p>
        </div>
        <a href="/settings#payment" class="flex-shrink-0 text-xs px-4 py-2 rounded-lg border font-medium" style="border-color:#F0C419; color:#F0C419;">
            View Documentation
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
const revenueData = @json($revenueData ?? array_fill(0, 12, 0));
const revenueLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

const rCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(rCtx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [
            { label: 'Revenue', data: revenueData, borderColor: '#F0C419', backgroundColor: 'rgba(240,196,25,0.1)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 0 },
            { label: 'Target', data: [500000, 500000, 500000, 500000, 1000000, 1000000, 1000000, 1500000, 1500000, 1500000, 1500000, 1500000], borderColor: 'rgba(255,255,255,0.2)', borderWidth: 1.5, borderDash: [4,4], tension: 0, pointRadius: 0, fill: false }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false }, tooltip: {
            backgroundColor: '#1B2337', titleColor: '#fff', bodyColor: '#94A3B8',
            padding: 10, cornerRadius: 8,
            callbacks: { label: (c) => ` Rp ${c.parsed.y.toLocaleString('id-ID')}` }
        }},
        scales: {
            y: { display: false },
            x: { grid: { display: false }, border: { display: false }, ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 9 } } }
        }
    }
});
</script>
@endpush
