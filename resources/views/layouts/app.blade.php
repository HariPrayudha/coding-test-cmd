<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sistem Pengajuan Pembiayaan' }} — PT Capella Multidana</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO CMD.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans selection:bg-[#E59E15] selection:text-slate-950">

    <!-- Floating Top-Right Toast Notifications with Animated Timer Bar Component -->
    <x-toast />

    <!-- Top Navigation Bar with CMD Brand Identity -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">
                <!-- Logo & Brand Name -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('loans.index') }}" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none">
                        <img src="{{ asset('assets/LOGO CMD.png') }}" 
                             alt="Logo Capella Multidana" 
                             class="h-8 sm:h-9 w-auto object-contain transition-transform group-hover:scale-105">
                        <div class="hidden sm:block border-l border-slate-200 pl-3">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Pembiayaan Kendaraan & Multiguna</span>
                            <span class="block text-sm font-bold text-slate-900 tracking-tight">PT Capella Multidana</span>
                        </div>
                    </a>
                </div>

                <!-- Right Action: User Profile, Role Badge & Logout (Pure by Login, No Role Switcher) -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- User Profile Pill -->
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#0B132B] text-[#E59E15] font-bold flex items-center justify-center text-xs shadow-xs ring-2 ring-amber-500/20 shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</div>
                                <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-md mt-0.5 {{ auth()->user()->isMarketing() ? 'bg-blue-50 text-blue-700 border border-blue-200/60' : 'bg-amber-50 text-amber-800 border border-amber-200/60' }}">
                                    {{ auth()->user()->roleLabel() }}
                                </span>
                            </div>

                            <!-- Logout Form Button -->
                            <form action="{{ route('logout') }}" method="POST" class="inline ml-1 sm:ml-2">
                                @csrf
                                <button type="submit" 
                                        title="Keluar dari Sistem"
                                        class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-4 py-2 rounded-xl bg-[#E59E15] hover:bg-[#D48B06] text-[#0B132B] font-bold text-xs shadow-xs transition">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900">PT Capella Multidana</span>
                <span>•</span>
                <span>Internal Financing Portal</span>
            </div>
            <div>
                <span>&copy; {{ date('Y') }} PT Capella Multidana. Hak Cipta Dilindungi.</span>
            </div>
        </div>
    </footer>

</body>
</html>
