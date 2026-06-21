@extends($layout ?? 'layouts.app')

@section('title', 'Vehicle Wash — Cuci Kendaraan Tanpa Antre')

@section('content')
<div class="bg-slate-50 text-slate-800 antialiased overflow-hidden">
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white py-20 px-6 overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute left-0 bottom-0 -translate-x-1/4 translate-y-1/4 w-96 h-96 bg-accent/5 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            {{-- Left: Text & CTA (7 Cols) --}}
            <div class="lg:col-span-7 space-y-6 text-left">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-accent/15 text-accent border border-accent/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                    Layanan Cuci Kendaraan Nomor 1 di Medan
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">
                    Cuci Kendaraan <span class="text-accent">Tanpa Antre</span>,<br class="hidden sm:inline"> Langsung dari Rumah Anda
                </h1>
                <p class="text-slate-300 text-base md:text-lg max-w-2xl leading-relaxed">
                    Nikmati kemudahan booking cuci mobil atau motor Anda via aplikasi. Teknisi profesional kami akan datang langsung ke lokasi Anda membawa seluruh peralatan cuci. Pantau progress secara real-time dengan sistem pembayaran cashless yang aman.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="/clean-vehicle-mobile.apk" download="clean-vehicle-mobile.apk" 
                       class="inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-dark text-slate-950 font-bold px-6 py-4 rounded-xl shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16.607 22H7.393c-.93 0-1.748-.61-2.025-1.503L2.073 9.497A2.083 2.083 0 014.098 7h15.804a2.083 2.083 0 012.025 2.497l-3.295 11.003c-.277.893-1.095 1.503-2.025 1.503zM5.9 5.2h12.2c.4 0 .7.3.7.7s-.3.7-.7.7H5.9c-.4 0-.7-.3-.7-.7s.3-.7.7-.7zm1.8-3.2h8.6c.4 0 .7.3.7.7s-.3.7-.7.7H7.7c-.4 0-.7-.3-.7-.7s.3-.7.7-.7z"/>
                        </svg>
                        Download Aplikasi (APK)
                    </a>
                    <a href="#cara-kerja" 
                       class="inline-flex items-center justify-center gap-2 border border-slate-700 hover:border-slate-500 text-white font-bold px-6 py-4 rounded-xl transition-all duration-200">
                        Lihat Cara Kerja
                    </a>
                </div>
            </div>

            {{-- Right: Phone Mockup & App Preview (5 Cols) --}}
            <div class="lg:col-span-5 flex justify-center">
                {{-- Phone Outer Frame --}}
                <div class="w-64 h-[480px] rounded-[36px] border-[8px] border-slate-800 bg-slate-900 shadow-2xl overflow-hidden relative flex flex-col flex-shrink-0 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                    {{-- Notch --}}
                    <div class="w-28 h-4 bg-slate-800 rounded-b-xl absolute top-0 left-1/2 -translate-x-1/2 z-30 flex items-center justify-center">
                        <div class="w-2 h-2 bg-slate-900 rounded-full"></div>
                    </div>
                    
                    {{-- Screen Contents --}}
                    <div class="flex-1 bg-slate-50 flex flex-col text-left text-[10px] select-none p-4 pt-6 relative overflow-hidden text-slate-850">
                        {{-- Mock App Header --}}
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <div>
                                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Layanan Mandiri</p>
                                <p class="text-[11px] font-black text-slate-800">Clean Vehicle App</p>
                            </div>
                            <div class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-[8px]">
                                CV
                            </div>
                        </div>

                        {{-- Mock OnoPay Wallet Card --}}
                        <div class="mt-3 bg-gradient-to-br from-indigo-600 to-indigo-900 text-white rounded-xl p-3.5 shadow-md relative overflow-hidden">
                            <div class="absolute right-0 bottom-0 translate-x-4 translate-y-4 w-12 h-12 bg-white/10 rounded-full blur-md"></div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold tracking-wider text-[7px] text-indigo-200">OnoPay E-Wallet</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            </div>
                            <p class="text-[7px] text-indigo-300">Total Saldo Aktif</p>
                            <p class="text-sm font-black mt-0.5">Rp 750.000</p>
                        </div>

                        {{-- Mock Vehicle List --}}
                        <div class="mt-4 space-y-2 flex-1">
                            <p class="font-bold text-slate-700 text-[8px] uppercase tracking-wider">Mobil Saya</p>
                            <div class="bg-white p-2 rounded-lg border border-slate-100 flex items-center gap-2 shadow-sm">
                                <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-[12px]">
                                    🚗
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-700 leading-none">Toyota Avanza</p>
                                    <p class="text-[7px] text-slate-400 mt-0.5">B 1234 CDG • Hitam</p>
                                </div>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-100 flex items-center gap-2 shadow-sm">
                                <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-[12px]">
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
                            <div class="bg-accent text-slate-900 font-extrabold text-center py-2.5 rounded-lg shadow-md">
                                Booking Layanan Sekarang
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics Bar --}}
    <section class="bg-white border-b border-slate-200 py-8 px-6 shadow-sm">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="space-y-1">
                <h3 class="text-3xl font-extrabold text-indigo-950">100+</h3>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Booking Sukses</p>
            </div>
            <div class="space-y-1 border-l border-slate-100">
                <h3 class="text-3xl font-extrabold text-indigo-950">4.8 / 5</h3>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Rating Kepuasan</p>
            </div>
            <div class="space-y-1 border-l border-slate-100">
                <h3 class="text-3xl font-extrabold text-indigo-950">Teknisi</h3>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Andal dan profesional</p>
            </div>
            <div class="space-y-1 border-l border-slate-100">
                <h3 class="text-3xl font-extrabold text-indigo-950">2+ Outlet</h3>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Cakupan Wilayah</p>
            </div>
        </div>
    </section>

    {{-- Benefits Section (Replaced Pengenalan Sistem) --}}
    <section id="fitur" class="py-20 px-6 max-w-7xl mx-auto text-center space-y-12">
        <div class="space-y-3">
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Kenapa Memilih Vehicle Wash?</h2>
            <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed">
                Kami mendefinisikan ulang cara merawat kendaraan dengan integrasi teknologi tinggi dan pelayanan premium di rumah Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Benefit 1 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 text-left space-y-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-800 text-base">Booking Cepat</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Daftarkan kendaraan, pilih jenis paket cuci, dan tentukan waktu pengerjaan hanya dalam hitungan detik.
                </p>
            </div>

            {{-- Benefit 2 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 text-left space-y-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-800 text-base">Teknisi Profesional</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Setiap teknisi dilatih secara intensif, bersertifikat, ramah, dan membawa seluruh air/alat cuci sendiri.
                </p>
            </div>

            {{-- Benefit 3 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 text-left space-y-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 002 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-800 text-base">Pembayaran Cashless</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Bayar instan bebas repot menggunakan saldo OnoPay E-Wallet atau scan QR Code statis yang otomatis terverifikasi.
                </p>
            </div>

            {{-- Benefit 4 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 text-left space-y-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-800 text-base">Pemantauan Real-Time</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pantau status cuci kendaraan Anda mulai dari keberangkatan teknisi, proses pengerjaan, hingga foto hasil akhir selesai.
                </p>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="cara-kerja" class="bg-slate-100 py-20 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Bagaimana Cara Kerjanya?</h2>
                <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Sistem pemesanan cuci mobil dan motor yang sangat praktis hanya dengan 4 langkah mudah.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                {{-- Arrow connector line for desktop --}}
                <div class="hidden md:block absolute top-1/2 left-4 right-4 h-0.5 bg-indigo-100 -translate-y-12 z-0"></div>

                {{-- Step 1 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center space-y-4 z-10">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mx-auto -mt-10 border-4 border-slate-100 shadow-sm">1</div>
                    <span class="text-3xl block">📱</span>
                    <h3 class="font-bold text-slate-850 text-base">Pilih Kendaraan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Daftarkan plat nomor dan jenis mobil atau motor Anda di profil pengguna sekali saja.</p>
                </div>

                {{-- Step 2 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center space-y-4 z-10">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mx-auto -mt-10 border-4 border-slate-100 shadow-sm">2</div>
                    <span class="text-3xl block">🧼</span>
                    <h3 class="font-bold text-slate-850 text-base">Pilih Paket Jasa</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pilih paket layanan cuci yang Anda butuhkan (Basic, Premium, atau Detailing lengkap).</p>
                </div>

                {{-- Step 3 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center space-y-4 z-10">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mx-auto -mt-10 border-4 border-slate-100 shadow-sm">3</div>
                    <span class="text-3xl block">📅</span>
                    <h3 class="font-bold text-slate-850 text-base">Tentukan Waktu</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Tentukan jam cuci, pilih teknisi favorit Anda, dan selesaikan pembayaran cashless.</p>
                </div>

                {{-- Step 4 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center space-y-4 z-10">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mx-auto -mt-10 border-4 border-slate-100 shadow-sm">4</div>
                    <span class="text-3xl block">✨</span>
                    <h3 class="font-bold text-slate-850 text-base">Kendaraan Bersih</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Teknisi profesional kami datang mengerjakan cuci kendaraan Anda di lokasi hingga bersih kilap.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- App Screenshots Section --}}
    <section class="py-20 px-6 max-w-7xl mx-auto text-center space-y-12">
        <div class="space-y-3">
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Jelajahi Aplikasi Kami</h2>
            <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed">
                Antarmuka modern, interaktif, dan mudah digunakan yang dirancang khusus untuk kenyamanan Anda.
            </p>
        </div>

        {{-- Screenshots Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            {{-- Slide 1: Dashboard --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="aspect-[9/16] rounded-xl bg-slate-900 text-white overflow-hidden p-3 text-left relative flex flex-col text-[8px]">
                    <div class="flex justify-between items-center border-b border-slate-800 pb-2 mb-3">
                        <span class="font-bold text-[9px]">Dashboard</span>
                        <span class="w-2 h-2 rounded-full bg-accent"></span>
                    </div>
                    <div class="bg-indigo-600 rounded-lg p-2.5 mb-3 text-white">
                        <p class="text-[6px] opacity-75">Halo, Josua</p>
                        <p class="text-[10px] font-black mt-0.5">Mau cuci kendaraan apa hari ini?</p>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg mb-2">
                        <p class="font-bold text-slate-300">Promo Aktif</p>
                        <p class="text-white font-extrabold text-[9px] mt-0.5">FIRST30 - Potongan 30%</p>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg flex-1">
                        <p class="font-bold text-slate-300">Menu Utama</p>
                        <div class="grid grid-cols-2 gap-1.5 mt-1 text-center">
                            <div class="bg-slate-700 p-1 rounded font-bold text-accent">🚗 Mobil</div>
                            <div class="bg-slate-700 p-1 rounded font-bold text-accent">🏍️ Motor</div>
                        </div>
                    </div>
                </div>
                <h4 class="font-bold text-slate-800 text-sm mt-3">Layar Dashboard</h4>
                <p class="text-[10px] text-slate-450 mt-1">Akses cepat menu pemesanan & info promo.</p>
            </div>

            {{-- Slide 2: Booking --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="aspect-[9/16] rounded-xl bg-slate-900 text-white overflow-hidden p-3 text-left relative flex flex-col text-[8px]">
                    <div class="flex justify-between items-center border-b border-slate-800 pb-2 mb-3">
                        <span class="font-bold text-[9px]">Pemesanan</span>
                        <span class="w-2 h-2 rounded-full bg-accent"></span>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg mb-2">
                        <p class="font-bold text-slate-300">Pilih Teknisi Terdekat</p>
                        <div class="mt-1 flex items-center gap-1.5 bg-slate-700/50 p-1.5 rounded">
                            <span class="text-[11px]">👨‍🔧</span>
                            <div>
                                <p class="font-bold text-white leading-none">Weril</p>
                                <p class="text-[6px] text-accent mt-0.5">⭐ 4.8 (120 Ulasan)</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg mb-2">
                        <p class="font-bold text-slate-300">Pilih Jadwal Cuci</p>
                        <div class="flex gap-1.5 mt-1">
                            <span class="bg-accent text-slate-950 p-1 rounded font-bold">Mon, 22 Jun</span>
                            <span class="bg-slate-700 p-1 rounded text-slate-300">Tue, 23 Jun</span>
                        </div>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg flex-1">
                        <p class="font-bold text-slate-300">Paket & Harga</p>
                        <p class="text-white font-extrabold text-[9px] mt-0.5">Premium Wash (Rp 120.000)</p>
                    </div>
                </div>
                <h4 class="font-bold text-slate-800 text-sm mt-3">Layar Booking</h4>
                <p class="text-[10px] text-slate-450 mt-1">Atur jadwal, pilih paket, dan teknisi.</p>
            </div>

            {{-- Slide 3: Tracking --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="aspect-[9/16] rounded-xl bg-slate-900 text-white overflow-hidden p-3 text-left relative flex flex-col text-[8px]">
                    <div class="flex justify-between items-center border-b border-slate-800 pb-2 mb-3">
                        <span class="font-bold text-[9px]">Lacak Status</span>
                        <span class="w-2 h-2 rounded-full bg-accent"></span>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg mb-2">
                        <p class="font-bold text-slate-300">Pekerjaan Cuci</p>
                        <p class="text-accent font-extrabold text-[9px] mt-0.5">Sedang Berjalan (In Progress)</p>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg flex-1 space-y-2">
                        <p class="font-bold text-slate-300">Timeline Kegiatan</p>
                        <div class="space-y-1.5 border-l border-slate-700 pl-2 ml-1 text-[7px] text-slate-400">
                            <div><span class="text-accent">✓</span> Booking Dikonfirmasi</div>
                            <div><span class="text-accent">✓</span> Teknisi Tiba di Lokasi</div>
                            <div class="text-white font-bold"><span class="text-accent">●</span> Sedang Cuci & Waxing</div>
                        </div>
                    </div>
                    <div class="bg-accent text-slate-950 font-bold p-1 rounded text-center">
                        Hubungi Teknisi via Chat
                    </div>
                </div>
                <h4 class="font-bold text-slate-800 text-sm mt-3">Layar Tracking</h4>
                <p class="text-[10px] text-slate-450 mt-1">Pantau status pengerjaan real-time.</p>
            </div>

            {{-- Slide 4: History --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="aspect-[9/16] rounded-xl bg-slate-900 text-white overflow-hidden p-3 text-left relative flex flex-col text-[8px]">
                    <div class="flex justify-between items-center border-b border-slate-800 pb-2 mb-3">
                        <span class="font-bold text-[9px]">Riwayat</span>
                        <span class="w-2 h-2 rounded-full bg-accent"></span>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg mb-2 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-white">Premium Wash</p>
                            <p class="text-slate-400 text-[6px]">Selesai • 22 Jun 2026</p>
                        </div>
                        <span class="bg-emerald-500/20 text-emerald-400 px-1 py-0.5 rounded text-[6px]">PAID</span>
                    </div>
                    <div class="bg-slate-800 p-2 rounded-lg flex-grow space-y-1">
                        <p class="font-bold text-slate-300">Beri Ulasan Layanan</p>
                        <div class="flex gap-1 justify-center text-accent text-[11px]">
                            ⭐ ⭐ ⭐ ⭐ ⭐
                        </div>
                        <p class="text-center text-[7px] text-slate-400">Ulasan sukses terkirim</p>
                    </div>
                </div>
                <h4 class="font-bold text-slate-800 text-sm mt-3">Layar Riwayat</h4>
                <p class="text-[10px] text-slate-450 mt-1">Lihat struk belanja & berikan rating.</p>
            </div>
        </div>
    </section>

    {{-- Pricing & Service Packages --}}
    <section id="layanan" class="bg-white py-20 px-6 border-y border-slate-200">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Paket Layanan & Harga</h2>
                <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Harga transparan tanpa ada biaya tersembunyi. Silakan pilih paket yang sesuai dengan kebutuhan kendaraan Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Package 1 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 flex flex-col space-y-6 relative hover:shadow-md transition-shadow">
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-slate-800 text-lg">Basic Wash</h3>
                        <p class="text-xs text-slate-500">Pembersihan luar kendaraan standar cepat</p>
                    </div>
                    <div class="pt-2">
                        <span class="text-3xl font-black text-indigo-950">Rp 20k - 50k</span>
                        <span class="text-xs text-slate-400">/ pengerjaan</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 flex-1">
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Cuci bodi luar bersih</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Shampoo premium & semir ban</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Pengeringan lap microfiber</li>
                        <li class="flex items-center gap-2 text-slate-400"><span class="opacity-30">✗</span> Wax body kilat</li>
                    </ul>
                    <a href="/clean-vehicle-mobile.apk" download="clean-vehicle-mobile.apk" 
                       class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-xs text-center shadow-md">
                        Order di Aplikasi
                    </a>
                </div>

                {{-- Package 2 (Premium - Recommended) --}}
                <div class="bg-white rounded-2xl border-2 border-indigo-600 shadow-lg p-8 flex flex-col space-y-6 relative hover:shadow-xl transition-shadow">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-wider">
                        Terpopuler
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-slate-800 text-lg">Premium Wash</h3>
                        <p class="text-xs text-slate-500">Pembersihan mendalam bodi & velg detail</p>
                    </div>
                    <div class="pt-2">
                        <span class="text-3xl font-black text-indigo-950">Rp 50k - 80k</span>
                        <span class="text-xs text-slate-400">/ pengerjaan</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 flex-1">
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Semua layanan Basic Wash</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Cuci detail bodi, kolong & velg</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Proteksi Wax Body kilat</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Vacuum interior kabin mobil</li>
                    </ul>
                    <a href="/clean-vehicle-mobile.apk" download="clean-vehicle-mobile.apk" 
                       class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl text-xs text-center shadow-md">
                        Order di Aplikasi
                    </a>
                </div>

                {{-- Package 3 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 flex flex-col space-y-6 relative hover:shadow-md transition-shadow">
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-slate-800 text-lg">Detailing Special</h3>
                        <p class="text-xs text-slate-500">Restorasi kaca, jamur bodi, & interior lengkap</p>
                    </div>
                    <div class="pt-2">
                        <span class="text-3xl font-black text-indigo-950">Rp 80k - 120k</span>
                        <span class="text-xs text-slate-400">/ pengerjaan</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 flex-1">
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Semua layanan Premium Wash</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Hilangkan jamur kaca & jamur bodi</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Dressing interior & ruang mesin</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Fogging interior antibakteri</li>
                    </ul>
                    <a href="/clean-vehicle-mobile.apk" download="clean-vehicle-mobile.apk" 
                       class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-xs text-center shadow-md">
                        Order di Aplikasi
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Coverage Area --}}
    <section class="py-20 px-6 max-w-7xl mx-auto space-y-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {{-- Left: Text (5 Cols) --}}
            <div class="lg:col-span-5 space-y-6 text-left">
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                Area Cakupan Jasa
            </h2>

            <p class="text-slate-500 text-xs md:text-sm leading-relaxed">
                Saat ini layanan kami tersedia untuk wilayah <strong>Kota Medan</strong> dan sekitarnya.
                Tim teknisi siap melayani pencucian kendaraan langsung ke lokasi pelanggan
                maupun di outlet resmi kami.
            </p>

            <div class="grid grid-cols-2 gap-4 text-xs font-bold text-slate-700">
                <div class="flex items-center gap-2">✔ Medan Kota</div>
                <div class="flex items-center gap-2">✔ Medan Baru</div>
                <div class="flex items-center gap-2">✔ Medan Sunggal</div>
                <div class="flex items-center gap-2">✔ Medan Johor</div>
                <div class="flex items-center gap-2">✔ Medan Selayang</div>
                <div class="flex items-center gap-2">✔ Medan Denai</div>
            </div>
        </div>

            {{-- Right: Map Medan (7 Cols) --}}
            <div class="lg:col-span-7 bg-white rounded-2xl p-2 border border-slate-200 shadow-sm overflow-hidden h-72">
                <iframe
                    src="https://maps.google.com/maps?q=Medan,%20Sumatera%20Utara&t=&z=11&ie=UTF8&iwloc=&output=embed"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>

    {{-- Testimonials / Social Proof --}}
    <section class="bg-slate-100 py-20 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Apa Kata Pengguna Kami?</h2>
                <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Lebih dari ribuan pengguna telah merasakan kepraktisan mencuci kendaraan tanpa perlu repot keluar rumah.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Testimonial 1 --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex text-accent text-sm">⭐ ⭐ ⭐ ⭐ ⭐</div>
                    <p class="text-xs text-slate-500 italic leading-relaxed">
                        "Praktis banget! Tinggal booking dari kantor pagi hari, pas pulang mobil udah bersih mengkilap di garasi rumah. Teknisinya sopan dan profesional."
                    </p>
                    <div>
                        <p class="font-extrabold text-slate-800 text-xs">Budi Santoso</p>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Jakarta Selatan</p>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex text-accent text-sm">⭐ ⭐ ⭐ ⭐ ⭐</div>
                    <p class="text-xs text-slate-500 italic leading-relaxed">
                        "Teknisinya ramah dan membawa peralatan lengkap sendiri. Hasil cucinya detail banget untuk paket Detailing, jamur kaca di mobil saya hilang semua!"
                    </p>
                    <div>
                        <p class="font-extrabold text-slate-800 text-xs">Susi Handayani</p>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Sawangan, Depok</p>
                    </div>
                </div>

                {{-- Testimonial 3 --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex text-accent text-sm">⭐ ⭐ ⭐ ⭐ ⭐</div>
                    <p class="text-xs text-slate-500 italic leading-relaxed">
                        "Pembayaran pake OnoPay sat-set tanpa ribet uang kembalian. Sering dapet cashback/diskon voucher cuci juga di aplikasi. Rekomendasi banget buat yang mager!"
                    </p>
                    <div>
                        <p class="font-extrabold text-slate-800 text-xs">Galih Prakoso</p>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider">Bekasi Barat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section id="faq" class="py-20 px-6 max-w-4xl mx-auto space-y-12">
        <div class="text-center space-y-3">
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-slate-500 leading-relaxed">Punya pertanyaan seputar layanan kami? Temukan jawabannya di bawah ini.</p>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <h4 class="font-extrabold text-slate-800 text-sm">Apakah saya perlu menyediakan air dan listrik sendiri?</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Tidak perlu! Teknisi kami membawa tangki air mandiri dan generator listrik sendiri, sehingga Anda cukup memarkirkan kendaraan di area yang cukup lapang.</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <h4 class="font-extrabold text-slate-800 text-sm">Berapa lama proses pencucian kendaraan berlangsung?</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Untuk paket Basic Wash berkisar antara 30-45 menit, Premium Wash berkisar 45-60 menit, sedangkan Detailing Special membutuhkan waktu 2-3 jam tergantung kondisi kendaraan.</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <h4 class="font-extrabold text-slate-800 text-sm">Bagaimana jika terjadi pembatalan jadwal booking?</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Anda dapat membatalkan pesanan secara mandiri di halaman riwayat transaksi sebelum teknisi berangkat tanpa biaya tambahan, dan dana OnoPay Anda akan dikembalikan secara penuh.</p>
            </div>
        </div>
    </section>
</div>
@endsection
