@props([
    'label',
    'value',
    'total',
])

@php
    // Proporção calculada a partir dos counts existentes (sem métrica nova).
    $value = (int) $value;
    $total = (int) $total;
    $percent = $total > 0 ? (int) round($value / $total * 100) : 0;
@endphp

{{-- Card de detalhe (secundário): número menor que os indicadores do topo,
     com a proporção "X de Y — Z%" e uma barrinha discreta. Clean: fundo branco,
     borda fina, sem sombra; roxo só como acento (número + barra). --}}
<div class="rounded-xl border border-gray-200 bg-white p-5">
    <div class="flex items-baseline justify-between gap-2">
        <p class="text-2xl font-semibold text-brand-700">{{ $value }}</p>
        <p class="text-xs font-medium text-gray-400">{{ $percent }}%</p>
    </div>

    <p class="mt-1 text-sm font-medium text-gray-600">{{ $label }}</p>
    <p class="mt-0.5 text-xs text-gray-400">{{ $value }} de {{ $total }}</p>

    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
        <div class="h-full rounded-full bg-brand-500" style="width: {{ $percent }}%"></div>
    </div>
</div>
