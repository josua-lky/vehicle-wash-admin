<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Vehicle Wash Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(145deg, #0F1724 0%, #1B2337 50%, #1E2B45 100%);
        }
        .dot-pattern {
            background-image: radial-gradient(rgba(240,196,25,0.08) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .input-field {
            width: 100%;
            padding: 11px 14px 11px 42px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            font-size: 14px;
            color: #1E293B;
            background: #F8FAFC;
            transition: all 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #F0C419;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(240,196,25,0.12);
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: #1B2337;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.02em;
        }
        .btn-primary:hover { background: #252D41; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(27,35,55,0.3); }
        .car-icon-glow { filter: drop-shadow(0 0 20px rgba(240,196,25,0.4)); }
        .feature-item { opacity: 0.75; }
    </style>
</head>
<body class="min-h-screen flex">

    {{-- ═══ LEFT PANEL (Dark) ═══ --}}
    <div class="hidden lg:flex lg:w-[45%] flex-col gradient-bg dot-pattern relative overflow-hidden">

        {{-- Decorative circles --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-5" style="background:#F0C419;"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full opacity-5" style="background:#F0C419;"></div>
        <div class="absolute top-1/2 -left-20 w-40 h-40 rounded-full opacity-5" style="background:#3B82F6;"></div>

        <div class="flex flex-col items-center justify-center flex-1 px-12 relative z-10">

            {{-- Logo --}}
            <div class="flex items-center gap-3 mb-12">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#F0C419;">
                    <svg class="w-7 h-7" fill="#1B2337" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-white font-bold text-xl tracking-tight">Vehicle Wash</h1>
                    <p class="text-xs" style="color:#7C8DB5; letter-spacing:0.05em;">ADMIN CONSOLE ACCESS</p>
                </div>
            </div>

            {{-- Main illustration (car wash SVG) --}}
            <div class="car-icon-glow mb-10">
                <svg width="220" height="160" viewBox="0 0 220 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Road/ground -->
                    <rect x="0" y="130" width="220" height="30" rx="4" fill="rgba(255,255,255,0.05)"/>
                    <!-- Car body -->
                    <rect x="30" y="85" width="160" height="45" rx="8" fill="rgba(240,196,25,0.15)" stroke="#F0C419" stroke-width="1.5"/>
                    <!-- Car roof -->
                    <path d="M65 85 L80 55 L140 55 L155 85" fill="rgba(240,196,25,0.1)" stroke="#F0C419" stroke-width="1.5" stroke-linejoin="round"/>
                    <!-- Windows -->
                    <rect x="85" y="60" width="25" height="22" rx="3" fill="rgba(59,130,246,0.3)" stroke="rgba(240,196,25,0.4)" stroke-width="1"/>
                    <rect x="115" y="60" width="25" height="22" rx="3" fill="rgba(59,130,246,0.3)" stroke="rgba(240,196,25,0.4)" stroke-width="1"/>
                    <!-- Wheels -->
                    <circle cx="68" cy="130" r="16" fill="#0F1724" stroke="#F0C419" stroke-width="2"/>
                    <circle cx="68" cy="130" r="8" fill="rgba(240,196,25,0.2)" stroke="#F0C419" stroke-width="1.5"/>
                    <circle cx="152" cy="130" r="16" fill="#0F1724" stroke="#F0C419" stroke-width="2"/>
                    <circle cx="152" cy="130" r="8" fill="rgba(240,196,25,0.2)" stroke="#F0C419" stroke-width="1.5"/>
                    <!-- Water drops -->
                    <circle cx="50" cy="40" r="3" fill="rgba(96,165,250,0.7)"/>
                    <circle cx="90" cy="25" r="4" fill="rgba(96,165,250,0.5)"/>
                    <circle cx="130" cy="35" r="3" fill="rgba(96,165,250,0.7)"/>
                    <circle cx="170" cy="20" r="5" fill="rgba(96,165,250,0.5)"/>
                    <circle cx="40" cy="65" r="2.5" fill="rgba(96,165,250,0.6)"/>
                    <circle cx="180" cy="50" r="3" fill="rgba(96,165,250,0.6)"/>
                    <!-- Spray lines -->
                    <line x1="20" y1="30" x2="20" y2="100" stroke="rgba(96,165,250,0.25)" stroke-width="1.5" stroke-dasharray="4 4"/>
                    <line x1="200" y1="25" x2="200" y2="95" stroke="rgba(96,165,250,0.25)" stroke-width="1.5" stroke-dasharray="4 4"/>
                </svg>
            </div>

            {{-- Title --}}
            <div class="text-center mb-10">
                <h2 class="text-white text-2xl font-bold mb-2">Kelola Operasional</h2>
                <h2 class="text-2xl font-bold mb-4" style="color:#F0C419;">dengan Mudah & Efisien</h2>
                <p class="text-sm leading-relaxed" style="color:#7C8DB5; max-width:320px;">
                    Platform manajemen cuci kendaraan terintegrasi — dari pemesanan hingga laporan keuangan dalam satu dashboard.
                </p>
            </div>

            {{-- Feature pills --}}
            <div class="flex flex-wrap justify-center gap-2">
                @foreach(['📊 Real-time Dashboard', '🔔 Notifikasi Otomatis', '📄 Laporan PDF/Excel', '👨‍🔧 Manajemen Teknisi', '💳 Payment Gateway'] as $feature)
                <span class="feature-item text-xs px-3 py-1.5 rounded-full" style="background:rgba(255,255,255,0.08); color:#A0AEC0; border:1px solid rgba(255,255,255,0.08);">
                    {{ $feature }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Bottom copyright --}}
        <div class="px-12 py-5 text-center">
            <p class="text-xs" style="color:#4A5568;">© {{ date('Y') }} Vehicle Wash. All rights reserved.</p>
        </div>
    </div>

    {{-- ═══ RIGHT PANEL (White) ═══ --}}
    <div class="flex-1 flex items-center justify-center bg-white px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#1B2337;">
                    <svg class="w-6 h-6" fill="#F0C419" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.01 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-800">Vehicle Wash Admin</p>
            </div>

            {{-- Back to landing button --}}
            <div class="mb-6">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-850 hover:underline transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda Publik
                </a>
            </div>

            {{-- Form header --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-1">Selamat Datang 👋</h2>
                <p class="text-sm text-slate-500">Masuk ke panel admin Vehicle Wash</p>
            </div>

            {{-- Session errors --}}
            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl text-sm text-red-700 flex items-start gap-2.5" style="background:#FEF2F2; border:1px solid #FECACA;">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Email Address</label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input-field" placeholder="admin@vehiclewash.id" required autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Password</label>
                        <a href="#" class="text-xs font-medium" style="color:#F0C419;">Lupa Password?</a>
                    </div>
                    <div class="relative" x-data="{ show: false }">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input :type="show ? 'text' : 'password'" name="password"
                               class="input-field pr-10" placeholder="••••••••" required>
                        <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2.5">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 rounded cursor-pointer" style="accent-color:#F0C419;">
                    <label for="remember" class="text-sm text-slate-600 cursor-pointer">Stay me in Dashboard</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary mt-2">
                    Sign In to Dashboard →
                </button>
            </form>

            {{-- Divider info --}}
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">
                    Akses terbatas untuk admin yang berwenang.<br>
                    Hubungi
                    <a href="mailto:it@vehiclewash.id" class="font-medium" style="color:#F0C419;">it@vehiclewash.id</a>
                    untuk bantuan.
                </p>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
