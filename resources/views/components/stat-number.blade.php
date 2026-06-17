@props([
    'label',
    'value',
])

{{-- Card de estatística com foco no NÚMERO: valor grande em destaque e rótulo
     discreto abaixo. Limpo: fundo branco, borda fina e sutil, sem sombra,
     bastante respiro interno. Roxo só como acento no número. --}}
<div class="rounded-xl border border-gray-200 bg-white p-6">
    <p class="text-4xl font-semibold tracking-tight text-brand-700">{{ $value }}</p>
    <p class="mt-2 text-sm font-medium text-gray-500">{{ $label }}</p>
</div>
