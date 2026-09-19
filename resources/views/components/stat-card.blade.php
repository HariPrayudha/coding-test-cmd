@props([
    'title',
    'value',
    'subtitle' => null,
    'variant' => 'default', // default, amber, emerald, rose
])

@php
    $cardClasses = 'bg-white rounded-2xl p-5 border shadow-xs transition-all duration-200 hover:shadow-md ';
    $iconBg = '';
    $iconColor = '';

    switch ($variant) {
        case 'amber':
            $cardClasses .= 'border-amber-200/70 hover:border-amber-300';
            $iconBg = 'bg-amber-50 text-amber-600 border border-amber-200/50';
            break;
        case 'emerald':
            $cardClasses .= 'border-emerald-200/70 hover:border-emerald-300';
            $iconBg = 'bg-emerald-50 text-emerald-600 border border-emerald-200/50';
            break;
        case 'rose':
            $cardClasses .= 'border-rose-200/70 hover:border-rose-300';
            $iconBg = 'bg-rose-50 text-rose-600 border border-rose-200/50';
            break;
        default:
            $cardClasses .= 'border-slate-200 hover:border-slate-300';
            $iconBg = 'bg-slate-100 text-slate-700 border border-slate-200/50';
            break;
    }
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $title }}</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $value }}</span>
            </div>
            @if ($subtitle)
                <p class="mt-1 text-xs text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
        @if (isset($icon))
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $iconBg }}">
                {{ $icon }}
            </div>
        @endif
    </div>
</div>
