<x-layouts.app title="Contratos" heading="Contratos">
    <x-slot:actions>
        <x-button :href="route('contracts.create')" variant="primary">+ Adicionar contrato</x-button>
    </x-slot:actions>

    <div class="space-y-4">
        {{-- Busca por título --}}
        <form method="GET" action="{{ route('contracts.index') }}" class="flex gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pesquisar por título"
                    class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                           placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                />
            </div>
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>

        <x-table :headers="['Status', 'Título', 'Cliente', 'Pacote', 'Vigência', 'Valor', '']">
            @forelse ($contracts as $contract)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <x-badge :variant="$contract->status->badgeVariant()">{{ $contract->status->label() }}</x-badge>
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $contract->title }}</td>
                    <td class="px-4 py-3">{{ $contract->client?->name }}</td>
                    <td class="px-4 py-3">{{ $contract->package?->name }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $contract->start_date?->format('d/m/Y') }}
                        <span class="mx-1 text-gray-400">–</span>
                        {{ $contract->end_date?->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">R$ {{ number_format((float) $contract->value, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <x-row-actions
                            :edit-url="route('contracts.edit', $contract)"
                            :delete-url="route('contracts.destroy', $contract)"
                            confirm="Excluir o contrato {{ $contract->title }}?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Nenhum contrato encontrado.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $contracts->links() }}
        </div>
    </div>
</x-layouts.app>
