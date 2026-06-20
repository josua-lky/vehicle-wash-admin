@extends('layouts.app')
@section('title', 'Detail Booking - ' . $booking->booking_code)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/bookings" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen Booking
        </a>
        <div class="flex gap-2">
            @if($booking->status === 'pending')
            <form method="POST" action="/bookings/{{ $booking->id }}/confirm" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#10B981;">
                    Konfirmasi Booking
                </button>
            </form>
            @endif
            @if(!in_array($booking->status, ['completed', 'cancelled', 'pending']))
            <form method="POST" action="/bookings/{{ $booking->id }}/complete" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#10B981;">
                    Selesaikan Booking
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
                        <span class="text-xs text-slate-400 font-mono">BOOKING CODE</span>
                        <h2 class="text-lg font-mono font-bold text-slate-800">{{ $booking->booking_code }}</h2>
                    </div>
                    @php
                        $statusBadge = ['completed'=>['Selesai','badge-green'],'in_progress'=>['Proses','badge-blue'],'pending'=>['Pending','badge-yellow'],'confirmed'=>['Konfirmasi','badge-purple'],'cancelled'=>['Batal','badge-red'],'assigned'=>['Ditugaskan','badge-gray']];
                        [$label,$class] = $statusBadge[$booking->status] ?? ['—','badge-gray'];
                    @endphp
                    <span class="badge {{ $class }} text-sm py-1.5 px-3">{{ $label }}</span>
                </div>

                <hr class="border-slate-100">

                @if($booking->status === 'cancelled')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-800 space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pesanan ini telah dibatalkan
                    </p>
                    <p class="text-xs text-red-600">Alasan pembatalan: <span class="font-medium text-red-800">{{ $booking->cancelled_reason ?: 'Tidak ada alasan khusus yang diisi.' }}</span></p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Jadwal Cuci</p>
                        <p class="font-semibold text-slate-700 mt-1">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jenis Layanan</p>
                        <p class="font-semibold text-slate-700 mt-1">{{ $booking->service_type === 'home' ? 'Home Service' : 'Outlet' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Kendaraan</p>
                        <p class="font-semibold text-slate-700 mt-1">{{ $booking->vehicle_name }} ({{ in_array($booking->vehicle_type, ['roda_2', 'motor']) ? 'Motor' : 'Mobil' }})</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Paket Cuci</p>
                        <p class="font-semibold text-slate-700 mt-1">{{ $booking->package ? $booking->package->name : '-' }}</p>
                    </div>
                </div>

                @if($booking->service_address)
                <div class="text-sm pt-2">
                    <p class="text-xs text-slate-400">Alamat Layanan</p>
                    <p class="font-semibold text-slate-700 mt-1">{{ $booking->service_address }}</p>
                </div>
                @endif

                @if($booking->notes)
                <div class="text-sm pt-2">
                    <p class="text-xs text-slate-400">Catatan Tambahan</p>
                    <p class="text-slate-600 mt-1">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>

            @if($booking->before_photo || $booking->after_photo)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm">Foto Hasil Kerja Teknisi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($booking->before_photo)
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sebelum Cuci (Before)</p>
                        <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm max-w-md">
                            <img src="{{ asset('storage/' . $booking->before_photo) }}" alt="Foto Sebelum" class="w-full h-48 object-cover">
                        </div>
                    </div>
                    @endif
                    @if($booking->after_photo)
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sesudah Cuci (After)</p>
                        <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm max-w-md">
                            <img src="{{ asset('storage/' . $booking->after_photo) }}" alt="Foto Sesudah" class="w-full h-48 object-cover">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Billing Details --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm">Rincian Pembayaran</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Subtotal</span>
                        <span class="font-medium text-slate-700">Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-red-500">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    <hr class="border-slate-100 my-1">
                    <div class="flex justify-between font-bold text-base text-slate-800">
                        <span>Total Tagihan</span>
                        <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($booking->status === 'completed' && $booking->review)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Ulasan Pelanggan
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $booking->review->rating ? 'text-amber-500' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="text-sm font-bold text-slate-800 ml-1.5">{{ $booking->review->rating }}.0 / 5.0</span>
                    </div>
                    @if($booking->review->comment)
                    <p class="text-sm text-slate-600 bg-slate-50 p-4 rounded-xl italic border border-slate-100">
                        "{{ $booking->review->comment }}"
                    </p>
                    @else
                    <p class="text-sm text-slate-400 italic">
                        Pengguna memberikan rating tanpa komentar tertulis.
                    </p>
                    @endif
                    <p class="text-xs text-slate-400">
                        Diulas pada {{ $booking->review->created_at ? $booking->review->created_at->format('d M Y, H:i') : '-' }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- Side Cards: Customer, Technician, Payment Status --}}
        <div class="space-y-6">
            {{-- Customer Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</h4>
                @if($booking->customer)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                        {{ strtoupper(substr($booking->customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $booking->customer->name }}</p>
                        <p class="text-xs text-slate-400">{{ $booking->customer->phone }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-slate-500">Tidak ada data pelanggan.</p>
                @endif
            </div>

            {{-- Technician Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Teknisi Ditugaskan</h4>
                @if($booking->technician)
                <div class="flex items-center gap-3 pb-2 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0 overflow-hidden"
                         style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                         @if($booking->technician->profile_photo)
                             <img src="{{ $booking->technician->avatar }}" alt="avatar" class="w-full h-full object-cover">
                         @else
                             {{ strtoupper(substr($booking->technician->name, 0, 1)) }}
                         @endif
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $booking->technician->name }}</p>
                        <p class="text-xs text-slate-400">{{ $booking->technician->phone }}</p>
                    </div>
                </div>
                @endif

                @if(!in_array($booking->status, ['completed', 'cancelled']))
                <form method="POST" action="/bookings/{{ $booking->id }}/assign" class="space-y-2.5 pt-1">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                            {{ $booking->technician ? 'Ganti Teknisi' : 'Tugaskan Teknisi' }}
                        </label>
                        <select name="technician_id" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50 text-slate-700 focus:bg-white focus:ring-1 focus:ring-slate-300 outline-none">
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ $booking->technician_id == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 text-xs font-semibold rounded-xl text-slate-900 transition-all active:scale-[0.98] shadow-sm hover:brightness-105" style="background:#F0C419;">
                        Simpan Penugasan
                    </button>
                </form>
                @elseif(!$booking->technician)
                <p class="text-xs text-slate-400 italic">Belum ada teknisi ditugaskan.</p>
                @endif
            </div>

            {{-- Payment Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Status Pembayaran</h4>
                @if($booking->payment)
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-400">{{ $booking->payment->payment_method }}</p>
                    </div>
                    @php
                        $payStatusBadge = ['paid'=>['Sukses','badge-green'],'pending'=>['Pending','badge-yellow'],'failed'=>['Gagal','badge-red'],'refunded'=>['Refund','badge-purple']];
                        [$plabel,$pclass] = $payStatusBadge[$booking->payment->status] ?? ['—','badge-gray'];
                    @endphp
                    <span class="badge {{ $pclass }}">{{ $plabel }}</span>
                </div>
                @else
                <p class="text-sm text-slate-500">Belum ada transaksi pembayaran.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
