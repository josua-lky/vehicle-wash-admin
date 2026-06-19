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
                    <a href="/dashboard" class="inline-flex items-center justify-center px-5 py-2 rounded-xl text-xs font-bold text-slate-900 bg-accent hover:bg-accent-dark transition-all shadow-sm">
                        Ke Panel Admin
                    </a>
                @else
                    <a href="/login" class="inline-flex items-center justify-center px-5 py-2 rounded-xl text-xs font-bold text-slate-900 bg-accent hover:bg-accent-dark transition-all shadow-sm">
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
    <footer class="bg-slate-900 text-slate-500 py-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
            <p>&copy; {{ date('Y') }} Vehicle Wash System. All rights reserved.</p>
            <p class="flex items-center gap-1">
                <span>Made for logistics efficiency</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </p>
        </div>
    </footer>

</body>
</html>
