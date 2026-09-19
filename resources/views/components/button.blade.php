@props([
    'variant' => 'primary', // primary, secondary, emerald, rose, ghost, outline
    'size' => 'md', // sm, md, lg, icon
    'type' => 'button',
    'as' => 'button', // button, a
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none';

    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-[11px] rounded-lg gap-1.5',
        'sm' => 'px-3 py-2 text-xs rounded-xl gap-1.5',
        'md' => 'px-4 py-2.5 text-xs sm:text-sm rounded-xl gap-2 shadow-xs',
        'lg' => 'px-5 py-3 text-sm sm:text-base rounded-xl gap-2.5 shadow-md',
        'icon' => 'w-8 h-8 rounded-lg p-1.5 justify-center',
        'icon-md' => 'w-9 h-9 rounded-xl p-2 justify-center',
        default => 'px-4 py-2.5 text-xs sm:text-sm rounded-xl gap-2 shadow-xs',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-[#E59E15] hover:bg-[#D48B06] active:bg-[#C67D04] text-slate-950 focus:ring-2 focus:ring-[#E59E15] focus:ring-offset-2',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 focus:ring-2 focus:ring-slate-300',
        'emerald', 'success' => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white shadow-xs focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2',
        'emerald-soft' => 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200',
        'rose', 'danger' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white shadow-xs focus:ring-2 focus:ring-rose-500 focus:ring-offset-2',
        'rose-soft' => 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200',
        'dark' => 'bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white shadow-xs focus:ring-2 focus:ring-slate-800',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900',
        default => 'bg-[#E59E15] hover:bg-[#D48B06] text-slate-950',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($as === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
