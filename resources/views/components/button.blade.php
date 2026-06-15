@props([
    'type' => 'button',
    'variant' => 'primary',
    'href' => null,
])

@php
    // Estilos base compartilhados por todas as variantes.
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold '
        .'transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 '
        .'disabled:cursor-not-allowed disabled:opacity-60';

    $variants = [
        'primary' => 'bg-brand-600 text-white hover:bg-brand-700 focus-visible:ring-brand-500',
        'accent' => 'bg-accent-500 text-white hover:bg-accent-600 focus-visible:ring-accent-500',
        'secondary' => 'border border-brand-200 bg-white text-brand-700 hover:bg-brand-50 focus-visible:ring-brand-500',
        'ghost' => 'text-brand-700 hover:bg-brand-50 focus-visible:ring-brand-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
