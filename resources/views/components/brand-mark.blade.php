{{-- Ícone "power-ring" da marca COINPEL: anel em degradê com a barra vertical no topo. --}}
<svg {{ $attributes->merge(['class' => 'h-8 w-8']) }} viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="coinpel-ring" x1="10" y1="6" x2="38" y2="42" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#5b2a86" />
            <stop offset="0.5" stop-color="#e5197e" />
            <stop offset="1" stop-color="#f7941d" />
        </linearGradient>
    </defs>
    {{-- Anel aberto no topo --}}
    <path d="M24 9 A15 15 0 1 0 39 24" stroke="url(#coinpel-ring)" stroke-width="6" stroke-linecap="round" />
    {{-- Barra vertical central --}}
    <line x1="24" y1="7" x2="24" y2="22" stroke="#5b2a86" stroke-width="6" stroke-linecap="round" />
</svg>
