@props([
    'type' => null,
    'status' => null,
])

@php
    $classes = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset shadow-2xs';

    if ($status instanceof \App\Enums\LoanStatus) {
        $classes .= ' ' . $status->badgeClasses();
        $label = $status->label();
    } elseif ($type instanceof \App\Enums\LoanType) {
        $classes .= ' ' . $type->badgeClasses();
        $label = $type->label();
    } else {
        $classes .= ' bg-slate-100 text-slate-700 ring-slate-600/10';
        $label = $slot;
    }
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($status && $status->isPending())
        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
    @elseif ($status && $status->isApproved())
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
    @elseif ($status && $status->isRejected())
        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
    @endif
    {{ $label }}
</span>
