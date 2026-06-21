<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Vehicle Wash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#F0C419',
                        'accent-dark': '#D4A017',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased min-h-screen flex flex-col justify-between">
    
    {{-- Header / Navbar --}}
    <header class="bg-slate-900 text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-accent">
                    <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-black text-sm tracking-tight block">Vehicle Wash</span>
                    <span class="text-[10px] text-slate-400 block -mt-1">Ekosistem Cuci Kendaraan</span>
                </div>
            </div>
            <div>
                @auth
                    <a href="/dashboard" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold text-slate-900 bg-accent hover:bg-accent-dark transition-all shadow-sm">
                        Ke Panel Admin
                    </a>
                @else
                    <a href="/login" class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg text-[11px] font-semibold text-slate-350 hover:text-white border border-slate-700 hover:border-slate-500 transition-all">
                        Login Admin
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-400 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            {{-- Column 1: Company Info --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-white">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-accent text-slate-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                        </svg>
                    </div>
                    <span class="font-extrabold text-base tracking-tight">Vehicle Wash</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Layanan cuci kendaraan panggilan terbaik di Indonesia. Kami menghadirkan teknisi profesional langsung ke lokasi Anda dengan pemesanan digital yang instan dan transparan.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.8c4.56-.93 8-4.96 8-9.8z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Column 2: Hubungi Kami --}}
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">Hubungi Kami</h4>
                <ul class="space-y-2 text-xs text-slate-400">
                    <li class="flex items-start gap-2">
                        <span class="text-accent">📍</span>
                        <span>Jl. Sudirman No.45, Senayan, Jakarta Selatan, 12190</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-accent">📞</span>
                        <span>021-1234-5678</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-accent">✉️</span>
                        <span>support@vehiclewash.id</span>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Menu Navigasi --}}
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">FAQ & Bantuan</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="#faq" class="text-slate-400 hover:text-white transition-colors">Pertanyaan Umum (FAQ)</a></li>
                    <li><a href="#cara-kerja" class="text-slate-400 hover:text-white transition-colors">Cara Kerja</a></li>
                    <li><a href="#layanan" class="text-slate-400 hover:text-white transition-colors">Layanan & Harga</a></li>
                </ul>
            </div>

            {{-- Column 4: Kebijakan & Legalitas --}}
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">Legalitas</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Syarat & Ketentuan Layanan</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Kebijakan Cookie</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 pt-8 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Vehicle Wash System. Hak Cipta Dilindungi.</p>
            <p class="flex items-center gap-1">
                <span>Cuci Kendaraan Panggilan No. 1</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </p>
        </div>
    </footer>

</body>
</html>
