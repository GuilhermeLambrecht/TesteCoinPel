@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

@php
    $base = 'flex items-center gap-3.5 rounded-lg px-4 py-3 text-base font-medium transition';
    $state = $active
        ? 'bg-brand-400/30 text-white'
        : 'text-brand-100/80 hover:bg-white/10 hover:text-white';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base.' '.$state]) }}>
    @isset($icon)
        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            {{ $icon }}
        </svg>
    @endisset
    <span>{{ $slot }}</span>
</a>
