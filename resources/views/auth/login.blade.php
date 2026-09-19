<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk | Portal Pembiayaan PT Capella Multidana</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO CMD.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full flex flex-col justify-center py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-50 via-white to-slate-100 text-slate-800 font-sans selection:bg-[#E59E15] selection:text-slate-950">

    <!-- Reusable Toast Notification (Floating Top-Right with Animated Timer) -->
    <x-toast />

    <!-- Centered Login Card with Everything Inside -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md"
        x-data="{
             email: '{{ old('email') }}',
             password: '',
             fillDemo(demoEmail, demoPass) {
                 this.email = demoEmail;
                 this.password = demoPass;
             }
         }">

        <div class="bg-white border border-slate-200/90 shadow-[0_20px_50px_rgba(0,0,0,0.06)] py-8 px-6 sm:px-10 rounded-3xl">

            <!-- Logo & Title (Inside the Card) -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-white shadow-xs border border-slate-200/80 mb-4">
                    <img src="{{ asset('assets/LOGO CMD.png') }}" alt="PT Capella Multidana" class="h-9 w-auto object-contain">
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900">
                    Portal Internal Pembiayaan
                </h1>
                <p class="mt-1.5 text-xs text-slate-500 font-medium">
                    Sistem Pencatatan & Persetujuan Kredit PT Capella Multidana
                </p>
            </div>

            <!-- Standard Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email Internal
                    </label>
                    <input type="email"
                        id="email"
                        name="email"
                        x-model="email"
                        required
                        autocomplete="email"
                        placeholder="nama@cmd.co.id"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder:text-slate-400 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#E59E15] focus:border-[#E59E15] transition">
                    @error('email')
                    <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <input type="password"
                        id="password"
                        name="password"
                        x-model="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder:text-slate-400 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#E59E15] focus:border-[#E59E15] transition">
                    @error('password')
                    <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 select-none">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#E59E15] focus:ring-[#E59E15]">
                        <span class="font-medium">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="primary" size="lg" class="w-full">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </form>

            <!-- Minimalist Demo Auto-Fill (Fills input, does not auto-login) -->
            <div class="mt-6 pt-5 border-t border-slate-200/90">
                <span class="block text-[11px] font-semibold text-slate-400 text-center uppercase tracking-wider mb-2.5">
                    Akun Pengujian (Klik untuk isi form)
                </span>

                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Demo Analyst (Approver) -->
                    <button type="button"
                        @click="fillDemo('analyst@cmd.co.id', 'password')"
                        title="Klik untuk mengisi email & sandi Credit Analyst"
                        class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/80 hover:bg-amber-50 hover:border-amber-300 transition text-center cursor-pointer group">
                        <span class="block text-xs font-bold text-slate-800 group-hover:text-amber-800">Credit Analyst</span>
                        <span class="inline-block text-[10px] font-semibold text-amber-700 bg-amber-100/70 px-1.5 py-0.5 rounded mt-0.5">Approver</span>
                    </button>

                    <!-- Demo Marketing (Maker) -->
                    <button type="button"
                        @click="fillDemo('marketing@cmd.co.id', 'password')"
                        title="Klik untuk mengisi email & sandi Marketing Officer"
                        class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/80 hover:bg-blue-50 hover:border-blue-300 transition text-center cursor-pointer group">
                        <span class="block text-xs font-bold text-slate-800 group-hover:text-blue-800">Marketing Officer</span>
                        <span class="inline-block text-[10px] font-semibold text-blue-700 bg-blue-100/70 px-1.5 py-0.5 rounded mt-0.5">Maker</span>
                    </button>
                </div>
            </div>

        </div>

        <p class="mt-6 text-center text-xs text-slate-400 font-medium">
            &copy; {{ date('Y') }} PT Capella Multidana. Hak Cipta Dilindungi.
        </p>
    </div>

</body>

</html>