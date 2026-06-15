<x-layouts.app title="Editar motorista" heading="Motoristas">
    <x-drawer title="Editar motorista" :close-url="route('drivers.index')">
        <form method="POST" action="{{ route('drivers.update', $driver) }}" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @include('drivers._form', ['driver' => $driver])
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Salvar alterações</x-button>
                <x-button :href="route('drivers.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
