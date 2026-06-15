@props([
    'src' => null,
    'name' => '',
    'size' => 'h-12 w-12',
])

@php
    // Avatar padrão quando não há foto: inicial do nome em tom neutro.
    $initial = mb_strtoupper(mb_substr(trim((string) $name), 0, 1));
@endphp

@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $name }}"
        {{ $attributes->merge(['class' => $size.' rounded-full object-cover']) }}
    />
@else
    <span
        role="img"
        aria-label="{{ $name !== '' ? $name : 'Sem foto' }}"
        {{ $attributes->merge(['class' => $size.' inline-flex select-none items-center justify-center rounded-full bg-gray-100 font-medium text-gray-500']) }}
    >
        {{ $initial !== '' ? $initial : '?' }}
    </span>
@endif
