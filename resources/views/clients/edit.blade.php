<x-layouts.app title="Editar cliente" heading="Clientes">
    <x-drawer title="Editar cliente" :close-url="route('clients.index')">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @include('clients._form', ['client' => $client])
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Salvar alterações</x-button>
                <x-button :href="route('clients.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
