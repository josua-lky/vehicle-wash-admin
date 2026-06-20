@extends('layouts.app')
@section('title', 'Detail Pembayaran - PAY-' . $payment->id)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/payments" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen Pembayaran
        </a>
        <div class="flex gap-2">
            @if($payment->status === 'paid')
            <form method="POST" action="/payments/{{ $payment->id }}/refund" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#EF4444;">
                    {{ $payment->refund_requested ? 'Setujui Refund' : 'Refund Pembayaran' }}
                </button>
            </form>
            @elseif($payment->status === 'pending')
            <form method="POST" action="/payments/{{ $payment->id }}/confirm" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#10B981;">
                    Konfirmasi Pembayaran
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- General Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs text-slate-400 font-mono">TRANSACTION ID</span>
                        <h2 class="text-lg font-mono font-bold text-slate-800">PAY-{{ $payment->id }}</h2>
                    </div>
                    @php
                        $payStatusBadge = ['paid'=>['Sukses','badge-green'],'pending'=>['Pending','badge-yellow'],'failed'=>['Gagal','badge-red'],'refunded'=>['Refund','badge-purple'],'expired'=>['Expired','badge-gray']];
                        [$label,$class] = $payStatusBadge[$payment->status] ?? ['—','badge-gray'];
                    @endphp
                    <div>
                        <span class="badge {{ $class }} text-sm py-1.5 px-3">{{ $label }}</span>
                        @if($payment->refund_requested)
                            <span class="badge badge-red text-sm py-1.5 px-3 ml-1">Butuh Refund</span>
                        @endif
                    </div>
                </div>

                <hr class="border-slate-100">

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Metode Pembayaran</p>
                        <p class="font-semibold text-slate-700 mt-1">OnoPay (E-Wallet)</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Provider Transaksi</p>
                        <p class="font-semibold text-slate-700 mt-1">{{ $payment->payment_provider ?: 'OnoPay Gateway' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Tanggal Pembayaran</p>
                        <p class="font-semibold text-slate-700 mt-1">
                            {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : ($payment->created_at ? $payment->created_at->format('d M Y, H:i') : '-') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Ref. Transaksi Gateway</p>
                        <p class="font-semibold text-slate-700 mt-1 font-mono text-xs">{{ $payment->transaction_id ?: '-' }}</p>
                    </div>
                </div>

                @if($payment->refunded_at)
                <hr class="border-slate-100">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Tanggal Refund</p>
                        <p class="font-semibold text-red-500 mt-1">{{ $payment->refunded_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jumlah Refund</p>
                        <p class="font-semibold text-red-500 mt-1">Rp {{ number_format($payment->refund_amount ?: $payment->amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Raw Gateway Response (expandable for debugging) --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-3" x-data="{ open: false }">
                <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                    <h3 class="font-bold text-slate-800 text-sm">Response Gateway (Raw JSON)</h3>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div x-show="open" x-cloak class="pt-2">
                    <pre class="bg-slate-50 p-4 rounded-xl text-xs overflow-x-auto text-slate-600 font-mono border border-slate-100 max-h-60">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) ?: '{}' }}</pre>
                </div>
            </div>
        </div>

        {{-- Side Cards: Booking & Customer Info --}}
        <div class="space-y-6">
            {{-- Booking Details --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Referensi Booking</h4>
                @if($payment->booking)
                <div class="space-y-2">
                    <p class="font-mono text-sm font-semibold text-slate-800">{{ $payment->booking->booking_code }}</p>
                    <div class="text-xs text-slate-500 space-y-1">
                        <p>Kendaraan: {{ $payment->booking->vehicle_name }}</p>
                        <p>Layanan: {{ $payment->booking->service_type === 'home' ? 'Home Service' : 'Outlet' }}</p>
                        <p>Jadwal: {{ $payment->booking->scheduled_at ? $payment->booking->scheduled_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <hr class="border-slate-50 my-2">
                    <a href="/bookings/{{ $payment->booking->id }}" class="block text-center text-xs font-semibold text-white py-2 rounded-lg" style="background:#1B2337;">
                        Buka Detail Booking →
                    </a>
                </div>
                @else
                <p class="text-sm text-slate-500">Tidak ada referensi booking.</p>
                @endif
            </div>

            {{-- Customer Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</h4>
                @if($payment->booking && $payment->booking->customer)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                        {{ strtoupper(substr($payment->booking->customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $payment->booking->customer->name }}</p>
                        <p class="text-xs text-slate-400">{{ $payment->booking->customer->phone }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-slate-500">Tidak ada data pelanggan.</p>
                @endif
            </div>

            {{-- Billing Summary --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Rincian Tagihan</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Nominal Transaksi</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    @if($payment->status === 'refunded')
                    <div class="flex justify-between text-red-500 font-semibold">
                        <span>Nominal Refund</span>
                        <span>- Rp {{ number_format($payment->refund_amount ?: $payment->amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
