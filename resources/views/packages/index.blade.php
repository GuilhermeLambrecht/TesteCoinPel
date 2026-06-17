<x-layouts.app title="Pacotes" :toolbar="true">
    <div class="space-y-6">
        <x-list-toolbar
            :create-url="route('packages.create')"
            create-label="+ Adicionar pacote"
            :action="route('packages.index')"
            :search="$search"
            placeholder="Pesquisar por nome ou destino"
        />

        <x-table :flush="true" :headers="['Nome', 'Destino', 'Duração', 'Preço', 'Status', '']">
            @forelse ($packages as $package)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $package->name }}</td>
                    <td class="px-4 py-3">{{ $package->destination }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $package->duration_days }} {{ $package->duration_days == 1 ? 'dia' : 'dias' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">R$ {{ number_format((float) $package->price, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        @if ($package->active)
                            <x-badge variant="success">Ativo</x-badge>
                        @else
                            <x-badge variant="neutral">Inativo</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-row-actions
                            :edit-url="route('packages.edit', $package)"
                            :delete-url="route('packages.destroy', $package)"
                            confirm="Excluir o pacote {{ $package->name }}?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                        Nenhum pacote encontrado.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $packages->links() }}
        </div>
    </div>
</x-layouts.app>
