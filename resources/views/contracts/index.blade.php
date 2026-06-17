<x-layouts.app title="Contratos" :toolbar="true">
    <div class="space-y-6">
        <x-list-toolbar
            :create-url="route('contracts.create')"
            create-label="+ Adicionar contrato"
            :action="route('contracts.index')"
            :search="$search"
            placeholder="Pesquisar por título"
        />

        <x-table :flush="true" :headers="['Status', 'Título', 'Cliente', 'Pacote', 'Vigência', 'Valor', '']">
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
