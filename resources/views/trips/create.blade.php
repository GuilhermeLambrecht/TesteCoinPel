<x-layouts.app title="Nova viagem" heading="Viagens">
    <x-drawer title="Nova viagem" :close-url="route('trips.index')">
        <form method="POST" action="{{ route('trips.store') }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @include('trips._form')
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Finalizar cadastro</x-button>
                <x-button :href="route('trips.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
