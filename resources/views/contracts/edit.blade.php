<x-layouts.app title="Editar contrato" heading="Contratos">
    <x-drawer title="Editar contrato" :close-url="route('contracts.index')">
        <form method="POST" action="{{ route('contracts.update', $contract) }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @include('contracts._form', ['contract' => $contract])
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Salvar alterações</x-button>
                <x-button :href="route('contracts.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
