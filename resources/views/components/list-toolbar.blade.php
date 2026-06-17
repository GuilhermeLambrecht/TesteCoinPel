@props([
    'createUrl',
    'createLabel',
    'action',
    'search' => '',
    'placeholder' => 'Pesquisar',
])

{{-- Barra superior das listagens: à esquerda "Adicionar" + "Filtrar"; ao centro/
     direita a busca (e filtros extras via slot); na ponta direita o admin + sair
     (que substituem o antigo topbar). "Adicionar" é um link e não submete o GET;
     o logout é um POST separado, fora do form de busca. Empilha no mobile. --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" action="{{ $action }}"
          class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <x-button :href="$createUrl" variant="primary">{{ $createLabel }}</x-button>
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            {{ $slot }}

            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="{{ $placeholder }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-brand-900 sm:w-72
                       placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
            />
        </div>
    </form>

    <x-user-menu class="shrink-0 sm:ml-4" />
</div>
