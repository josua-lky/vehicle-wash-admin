@extends($layout ?? 'layouts.app')

@section('title', 'Pengenalan Sistem')

@section('content')
<div class="py-8 px-6 max-w-7xl mx-auto space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Pengenalan Sistem</h1>
            <p class="text-slate-500 mt-1">Panduan lengkap ekosistem aplikasi Vehicle Wash & fitur unduhan aplikasi mobile.</p>
        </div>
        <div class="flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Sistem Aktif & Terintegrasi
            </span>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Left: Description & App Info (7 Cols) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Welcome Alert --}}
            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden">
                <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute left-1/3 bottom-0 translate-y-12 w-32 h-32 bg-amber-400/10 rounded-full blur-xl"></div>
                
                <h2 class="text-xl font-bold mb-2">Selamat Datang di Ekosistem Vehicle Wash!</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Sistem ini dirancang khusus untuk mendigitalisasi proses layanan cuci kendaraan secara end-to-end, menghubungkan pemilik kendaraan dengan teknisi cuci secara real-time, didukung transaksi cashless terintegrasi.
                </p>
            </div>

            {{-- 3 Core Modules --}}
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Komponen Utama Ekosistem</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Card 1: Web Admin --}}
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Console Web Admin</h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Panel kontrol pusat bagi administrator untuk memonitor booking, mengatur slot cuci, teknisi, paket layanan, data promo, dan laporan transaksi keuangan.
                        </p>
                    </div>

                    {{-- Card 2: Mobile Client --}}
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Aplikasi Mobile</h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Aplikasi khusus pelanggan untuk mendaftarkan kendaraan, melakukan booking cuci kendaraan sesuai slot, memakai kode promo, dan memantau status pengerjaan.
                        </p>
                    </div>

                    {{-- Card 3: OnoPay --}}
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">OnoPay Gateway</h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Gerbang pembayaran cashless mandiri. Memungkinkan pengguna melakukan pembayaran instan berbasis saldo dan QR code secara aman tanpa biaya admin tambahan.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Installation Guide --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Cara Instalasi APK di Android
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold flex-shrink-0">1</div>
                        <div>
                            <p class="font-semibold text-slate-700">Unduh Berkas</p>
                            <p class="text-slate-500 mt-1">Unduh berkas APK langsung menggunakan tombol download di sebelah kanan.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold flex-shrink-0">2</div>
                        <div>
                            <p class="font-semibold text-slate-700">Izinkan Sumber Luar</p>
                            <p class="text-slate-500 mt-1">Buka Pengaturan HP > Keamanan, lalu aktifkan opsi "Izinkan Instalasi dari Sumber Tak Dikenal".</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold flex-shrink-0">3</div>
                        <div>
                            <p class="font-semibold text-slate-700">Pasang & Mulai</p>
                            <p class="text-slate-500 mt-1">Buka file manager Anda, cari file APK yang diunduh, klik pasang, lalu buka aplikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: App Download Section & Phone Mockup (5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Download Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col items-center p-6 text-center space-y-6">
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-slate-800">Dapatkan Aplikasi Mobile</h3>
                    <p class="text-xs text-slate-500 max-w-xs leading-normal">Unduh aplikasi pelanggan sekarang untuk menguji alur pemesanan langsung dari smartphone Anda.</p>
                </div>

                {{-- Phone Mockup --}}
                <div class="w-60 h-96 rounded-[32px] border-[6px] border-slate-800 bg-slate-900 shadow-xl overflow-hidden relative flex flex-col flex-shrink-0">
                    {{-- Notch --}}
                    <div class="w-24 h-4 bg-slate-800 rounded-b-xl absolute top-0 left-1/2 -translate-x-1/2 z-30 flex items-center justify-center">
                        <div class="w-2 h-2 bg-slate-900 rounded-full"></div>
                    </div>
                    
                    {{-- Screen Contents --}}
                    <div class="flex-1 bg-slate-50 flex flex-col text-left text-[10px] select-none p-3 pt-6 relative overflow-hidden">
                        {{-- Mock App Header --}}
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <div>
                                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Layanan Mandiri</p>
                                <p class="text-[11px] font-black text-[#1b2337]">Clean Vehicle App</p>
                            </div>
                            <div class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-[8px]">
                                CV
                            </div>
                        </div>

                        {{-- Mock OnoPay Wallet Card --}}
                        <div class="mt-3 bg-gradient-to-br from-indigo-600 to-indigo-950 text-white rounded-xl p-3 shadow-md relative overflow-hidden">
                            <div class="absolute right-0 bottom-0 translate-x-4 translate-y-4 w-12 h-12 bg-white/10 rounded-full blur-md"></div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold tracking-wider text-[7px] text-indigo-200">OnoPay E-Wallet</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            </div>
                            <p class="text-[7px] text-indigo-300">Total Saldo Aktif</p>
                            <p class="text-sm font-black mt-0.5">Rp 750.000</p>
                        </div>

                        {{-- Mock Vehicle List --}}
                        <div class="mt-3 space-y-1.5 flex-1">
                            <p class="font-bold text-slate-700 text-[8px] uppercase tracking-wider">Mobil Saya</p>
                            <div class="bg-white p-2 rounded-lg border border-slate-100 flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-[#1b2337]">
                                    🚗
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-700 leading-none">Toyota Avanza</p>
                                    <p class="text-[7px] text-slate-400 mt-0.5">B 1234 CDG • Hitam</p>
                                </div>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-100 flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-[#1b2337]">
                                    🏍️
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-700 leading-none">Honda Beat</p>
                                    <p class="text-[7px] text-slate-400 mt-0.5">B 5678 EFG • Putih</p>
                                </div>
                            </div>
                        </div>

                        {{-- Mock Booking Button --}}
                        <div class="mt-auto">
                            <div class="bg-amber-400 text-[#1b2337] font-extrabold text-center py-2 rounded-lg shadow-sm">
                                Booking Layanan Sekarang
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Download Button --}}
                <div class="w-full space-y-2">
                    <a href="/clean-vehicle-mobile.apk" 
                       download="clean-vehicle-mobile.apk"
                       class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 text-white font-extrabold py-3 px-6 rounded-xl shadow-md transition-all duration-200 transform active:scale-98">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16.607 22H7.393c-.93 0-1.748-.61-2.025-1.503L2.073 9.497A2.083 2.083 0 014.098 7h15.804a2.083 2.083 0 012.025 2.497l-3.295 11.003c-.277.893-1.095 1.503-2.025 1.503zM5.9 5.2h12.2c.4 0 .7.3.7.7s-.3.7-.7.7H5.9c-.4 0-.7-.3-.7-.7s.3-.7.7-.7zm1.8-3.2h8.6c.4 0 .7.3.7.7s-.3.7-.7.7H7.7c-.4 0-.7-.3-.7-.7s.3-.7.7-.7z"/>
                        </svg>
                        Download APK (Android)
                    </a>
                    <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                        <span>Ukuran File: 44.5 MB</span>
                        <span>•</span>
                        <span>Versi: 1.0.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
