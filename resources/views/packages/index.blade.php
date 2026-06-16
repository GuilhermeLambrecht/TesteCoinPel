<x-layouts.app title="Pacotes" heading="Pacotes">
    <x-slot:actions>
        <x-button :href="route('packages.create')" variant="primary">+ Adicionar pacote</x-button>
    </x-slot:actions>

    <div class="space-y-4">
        {{-- Busca por nome/destino --}}
        <form method="GET" action="{{ route('packages.index') }}" class="flex gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pesquisar por nome ou destino"
                    class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                           placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                />
            </div>
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>

        <x-table :headers="['Nome', 'Destino', 'Duração', 'Preço', 'Status', '']">
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
