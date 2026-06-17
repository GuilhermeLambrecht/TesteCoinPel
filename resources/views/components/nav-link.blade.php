@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
    'iconImage' => null,
])

@php
    $base = 'flex items-center gap-3.5 rounded-lg px-3.5 py-3 text-base font-medium transition';
    $state = $active
        ? 'bg-brand-400/30 text-white'
        : 'text-brand-100/80 hover:bg-white/10 hover:text-white';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base.' '.$state]) }}>
    @if ($iconImage)
        {{-- Ícone como imagem; brightness-0 + invert deixa o ícone branco para
             contrastar com o fundo roxo da sidebar, no mesmo tamanho do SVG. --}}
        <img src="{{ $iconImage }}" alt="" class="h-6 w-6 shrink-0 object-contain brightness-0 invert" />
    @elseif (isset($icon))
        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            {{ $icon }}
        </svg>
    @endif
    <span>{{ $slot }}</span>
</a>
