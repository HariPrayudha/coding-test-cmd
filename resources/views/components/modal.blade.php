@props([
    'show' => 'false',
    'onClose' => '',
    'title' => null,
    'subtitle' => null,
    'maxWidth' => '2xl', // sm, md, lg, xl, 2xl, 3xl
    'headerVariant' => 'navy', // navy, white, rose, emerald
])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '3xl' => 'max-w-3xl',
        default => 'max-w-2xl',
    };

    $headerClasses = match($headerVariant) {
        'white' => 'bg-white text-slate-900 border-b border-slate-200',
        'rose' => 'bg-rose-50 text-rose-950 border-b border-rose-200',
        'emerald' => 'bg-emerald-50 text-emerald-950 border-b border-emerald-200',
        default => 'bg-[#0B132B] text-white border-b border-slate-800',
    };
@endphp

<div x-show="{{ $show }}" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <!-- Animated Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" 
         x-show="{{ $show }}"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="{{ $onClose ?: $show . ' = false' }}"></div>

    <!-- Centering wrapper -->
    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <!-- Animated Modal Panel -->
        <div class="relative flex flex-col max-h-[90vh] overflow-hidden rounded-3xl bg-white text-left shadow-2xl w-full {{ $maxWidthClass }} border border-slate-200"
             x-show="{{ $show }}"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            @if ($title || isset($headerIcon))
                <!-- Sticky Header -->
                <div class="sticky top-0 z-20 shrink-0 px-6 py-4 sm:py-5 flex items-center justify-between {{ $headerClasses }}">
                    <div class="flex items-center gap-3">
                        @if (isset($headerIcon))
                            {{ $headerIcon }}
                        @endif
                        <div>
                            <h3 class="text-base sm:text-lg font-bold leading-tight">{{ $title }}</h3>
                            @if ($subtitle)
                                <p class="text-[11px] {{ $headerVariant === 'navy' ? 'text-slate-400' : 'text-slate-500' }} mt-0.5">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <button @click="{{ $onClose ?: $show . ' = false' }}" 
                            type="button" 
                            class="{{ $headerVariant === 'navy' ? 'text-slate-400 hover:text-white' : 'text-slate-400 hover:text-slate-700' }} transition cursor-pointer p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Modal Body (Scrollable if content is long) -->
            <div class="overflow-y-auto flex-1">
                {{ $slot }}
            </div>

            @if (isset($footer))
                <!-- Sticky Footer -->
                <div class="sticky bottom-0 z-20 shrink-0 bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-3">
                    {{ $footer }}
                </div>
            @endif

        </div>
    </div>
</div>
