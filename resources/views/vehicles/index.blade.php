<x-layouts.app title="Veículos" heading="Veículos">
    <x-slot:actions>
        <x-button :href="route('vehicles.create')" variant="primary">+ Adicionar veículo</x-button>
    </x-slot:actions>

    <div class="space-y-4">
        {{-- Busca por placa/modelo --}}
        <form method="GET" action="{{ route('vehicles.index') }}" class="flex gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pesquisar por placa ou modelo"
                    class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                           placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                />
            </div>
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>

        <x-table :headers="['Placa', 'Modelo', 'Marca', 'Capacidade', 'Ano', 'Status', '']">
            @forelse ($vehicles as $vehicle)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $vehicle->plate }}</td>
                    <td class="px-4 py-3">{{ $vehicle->model }}</td>
                    <td class="px-4 py-3">{{ $vehicle->brand }}</td>
                    <td class="px-4 py-3">{{ $vehicle->capacity }}</td>
                    <td class="px-4 py-3">{{ $vehicle->year }}</td>
                    <td class="px-4 py-3">
                        @if ($vehicle->active)
                            <x-badge variant="success">Ativo</x-badge>
                        @else
                            <x-badge variant="neutral">Inativo</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-row-actions
                            :edit-url="route('vehicles.edit', $vehicle)"
                            :delete-url="route('vehicles.destroy', $vehicle)"
                            confirm="Excluir o veículo {{ $vehicle->plate }}?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Nenhum veículo encontrado.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $vehicles->links() }}
        </div>
    </div>
</x-layouts.app>
