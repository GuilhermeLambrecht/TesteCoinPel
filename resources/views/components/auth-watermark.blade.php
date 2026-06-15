{{-- Ilustração do ônibus como marca d'água translúcida sobre o painel roxo.
     O SVG tem cores chumbadas (imagens embutidas), então o efeito monocromático
     vem por CSS: baixa opacidade + grayscale + mix-blend-luminosity, deixando o
     roxo do tema (bg-brand-600) vazar por trás. Ancorada na base, não recebe
     cliques e não distorce a proporção (object-contain). --}}
<img
    src="{{ asset('images/Group_313.svg') }}"
    alt=""
    aria-hidden="true"
    {{ $attributes->merge([
        'class' => 'pointer-events-none absolute bottom-0 left-1/2 z-0 w-[105%] max-w-none -translate-x-1/2 '
            .'select-none object-contain opacity-[0.15] grayscale mix-blend-luminosity',
    ]) }} />