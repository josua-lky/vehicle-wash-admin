<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: true, page: '{{ request()->segment(1) ?: 'dashboard' }}' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vehicle Wash') — Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sidebar: '#1B2337',
                        'sidebar-hover': '#252D41',
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .nav-item { transition: all 0.15s ease; }
        .nav-active { background: #252D41; border-left: 3px solid #F0C419; }
        .nav-item:not(.nav-active):hover { background: rgba(255,255,255,0.05); }
        .sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 2px; }
        .table-row:hover { background: #F8FAFC; }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; }
        .badge-green  { background: #D1FAE5; color: #065F46; }
        .badge-yellow { background: #FEF3C7; color: #92400E; }
        .badge-blue   { background: #DBEAFE; color: #1E40AF; }
        .badge-red    { background: #FEE2E2; color: #991B1B; }
        .badge-gray   { background: #F1F5F9; color: #475569; }
        .badge-purple { background: #EDE9FE; color: #5B21B6; }
        .modal-overlay { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        input:focus, select:focus, textarea:focus { outline: none; box-shadow: 0 0 0 2px #F0C419; }
        .accent-btn { background: #F0C419; color: #1B2337; }
        .accent-btn:hover { background: #D4A017; }
        .toast { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-100">
<div class="flex h-screen overflow-hidden">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside class="flex flex-col fixed h-full z-20 transition-all duration-300"
           :class="sidebarOpen ? 'w-60' : 'w-16'"
           style="background:#1B2337;">

        {{-- Logo --}}
        <div class="flex items-center px-4 h-16 border-b border-white/5 flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#F0C419;">
                    <svg class="w-5 h-5" fill="#1B2337" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
                <div x-show="sidebarOpen" x-cloak>
                    <p class="text-white font-bold text-sm leading-tight">Vehicle Wash</p>
                    <p class="text-xs" style="color:#7C8DB5;">Admin Console</p>
                </div>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="ml-auto text-slate-400 hover:text-white p-1 rounded flex-shrink-0" x-show="sidebarOpen" x-cloak>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 px-2 space-y-0.5 overflow-y-auto sidebar-scrollbar">
            @php
                $currentRoute = request()->segment(1) ?: 'dashboard';
                $navItems = [
                    ['route' => 'dashboard',    'label' => 'Dashboard',             'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'technicians',  'label' => 'Manajemen Teknisi',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'slots',        'label' => 'Slot Cuci',             'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'bookings',     'label' => 'Manajemen Booking',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    ['route' => 'payments',     'label' => 'Pembayaran',            'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                    ['route' => 'promos',       'label' => 'Promo',                 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                    ['route' => 'reports',      'label' => 'Laporan',               'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ];
            @endphp

            @foreach($navItems as $item)
            <a href="/{{ $item['route'] }}"
               class="nav-item flex items-center gap-3 rounded-lg cursor-pointer group"
               :class="sidebarOpen ? 'px-3 py-2.5' : 'px-0 py-2.5 justify-center'"
               style="{{ $currentRoute === $item['route'] ? 'background:#252D41; border-left: 3px solid #F0C419;' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0 {{ $currentRoute === $item['route'] ? 'text-accent' : 'text-slate-400 group-hover:text-slate-200' }}"
                     :style="sidebarOpen ? '' : 'margin: 0 auto;'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak
                      class="text-sm font-medium truncate {{ $currentRoute === $item['route'] ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">
                    {{ $item['label'] }}
                </span>
            </a>
            @endforeach

            {{-- Divider --}}
            <div class="my-2" style="height:1px; background:rgba(255,255,255,0.07);"></div>

            {{-- Customers --}}
            <a href="/customers"
               class="nav-item flex items-center gap-3 rounded-lg cursor-pointer group"
               :class="sidebarOpen ? 'px-3 py-2.5' : 'px-0 py-2.5 justify-center'">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-400 group-hover:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak class="text-sm font-medium text-slate-400 group-hover:text-slate-200 truncate">Pelanggan</span>
            </a>

            {{-- Outlets --}}
            <a href="/outlets"
               class="nav-item flex items-center gap-3 rounded-lg cursor-pointer group"
               :class="sidebarOpen ? 'px-3 py-2.5' : 'px-0 py-2.5 justify-center'">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-400 group-hover:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak class="text-sm font-medium text-slate-400 group-hover:text-slate-200 truncate">Outlet</span>
            </a>

            {{-- Settings --}}
            <a href="/settings"
               class="nav-item flex items-center gap-3 rounded-lg cursor-pointer group"
               :class="sidebarOpen ? 'px-3 py-2.5' : 'px-0 py-2.5 justify-center'">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-400 group-hover:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak class="text-sm font-medium text-slate-400 group-hover:text-slate-200 truncate">Pengaturan</span>
            </a>
        </nav>

        {{-- User Profile + Logout --}}
        <div class="flex-shrink-0 border-t px-3 py-3 space-y-1" style="border-color:rgba(255,255,255,0.07);">
            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="nav-item w-full flex items-center gap-3 rounded-lg cursor-pointer group"
                        :class="sidebarOpen ? 'px-3 py-2.5' : 'px-0 py-2.5 justify-center'">
                    <svg class="w-5 h-5 flex-shrink-0 text-red-400 group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="text-sm font-medium text-red-400 group-hover:text-red-300">Logout</span>
                </button>
            </form>

            {{-- Profile --}}
            <div class="flex items-center gap-3 mt-2" :class="sidebarOpen ? 'px-2' : 'justify-center'">
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-sm font-bold text-white"
                     style="background:linear-gradient(135deg,#F0C419,#E67E22);">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div x-show="sidebarOpen" x-cloak class="min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs truncate" style="color:#7C8DB5;">{{ auth()->user()->role ?? 'Super Admin' }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ══════════════ MAIN CONTENT ══════════════ --}}
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="sidebarOpen ? 'ml-60' : 'ml-16'">

        {{-- Top Header --}}
        <header class="bg-white border-b border-slate-200 flex items-center h-14 px-6 gap-4 sticky top-0 z-10 flex-shrink-0">
            {{-- Toggle (mobile) --}}
            <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-slate-600 lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Search --}}
            <form action="{{ route('global-search') }}" method="GET" class="relative flex-1 max-w-sm">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pesanan, teknisi, pelanggan..."
                       class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-600 placeholder-slate-400 focus:bg-white focus:border-accent transition-colors">
            </form>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Notification --}}
                <button class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                {{-- Date --}}
                <div class="text-xs text-slate-400 hidden sm:block">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>

                {{-- Divider --}}
                <div class="w-px h-6 bg-slate-200"></div>

                {{-- User avatar --}}
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                         style="background:linear-gradient(135deg,#F0C419,#E67E22);">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold text-slate-700">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-slate-400">{{ auth()->user()->role ?? 'Super Admin' }}</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="toast fixed top-4 right-4 z-50 flex items-center gap-3 bg-white border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-lg text-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="toast fixed top-4 right-4 z-50 flex items-center gap-3 bg-white border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-lg text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-slate-100">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
