@props([
    // Mantido por compatibilidade com as chamadas (ex.: status da viagem),
    // mas não influencia mais a cor: o estilo é monocromático.
    'variant' => 'neutral',
])

@php
    // Estilo monocromático alinhado ao Figma: o status é comunicado apenas pelo
    // texto, em tom neutro, sem fundo colorido nem pílula.
    $classes = 'inline-flex items-center gap-1.5 text-sm font-medium text-gray-700';
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
