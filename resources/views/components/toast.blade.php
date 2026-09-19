<!-- Floating Top-Right Toast Notifications with Animated Timer Bar -->
<div class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    @if (session('success'))
    <div x-data="{ show: true, progress: 100, interval: null }"
        x-init="
            interval = setInterval(() => {
                progress -= 2.5;
                if (progress <= 0) {
                    show = false;
                    clearInterval(interval);
                }
            }, 100);
         "
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="pointer-events-auto overflow-hidden bg-white rounded-2xl shadow-xl border border-emerald-200 p-4 relative"
        role="alert">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="flex-1 pr-2">
                <h5 class="text-xs font-bold text-slate-900">Berhasil</h5>
                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
            <button @click="show = false; clearInterval(interval)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <!-- Timer Progress Line -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-100">
            <div class="h-full bg-emerald-500 transition-all duration-100 ease-linear" :style="`width: ${progress}%`"></div>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div x-data="{ show: true, progress: 100, interval: null }"
        x-init="
            interval = setInterval(() => {
                progress -= 2.5;
                if (progress <= 0) {
                    show = false;
                    clearInterval(interval);
                }
            }, 100);
         "
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="pointer-events-auto overflow-hidden bg-white rounded-2xl shadow-xl border border-rose-200 p-4 relative"
        role="alert">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1 pr-2">
                <h5 class="text-xs font-bold text-slate-900">Pemberitahuan</h5>
                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ session('error') }}</p>
            </div>
            <button @click="show = false; clearInterval(interval)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <!-- Timer Progress Line -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-100">
            <div class="h-full bg-rose-500 transition-all duration-100 ease-linear" :style="`width: ${progress}%`"></div>
        </div>
    </div>
    @endif
</div>