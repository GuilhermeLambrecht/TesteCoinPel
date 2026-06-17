<x-layouts.app title="Veículos" :toolbar="true">
    <div class="space-y-6">
        <x-list-toolbar
            :create-url="route('vehicles.create')"
            create-label="+ Adicionar veículo"
            :action="route('vehicles.index')"
            :search="$search"
            placeholder="Pesquisar por placa ou modelo"
        />

        <x-table :flush="true" :headers="['Placa', 'Modelo', 'Marca', 'Capacidade', 'Ano', 'Status', '']">
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
