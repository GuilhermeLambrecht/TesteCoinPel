<x-layouts.app title="Clientes" heading="Clientes">
    <x-slot:actions>
        <x-button :href="route('clients.create')" variant="primary">+ Adicionar cliente</x-button>
    </x-slot:actions>

    <div class="space-y-4">
        {{-- Busca por nome/e-mail/documento --}}
        <form method="GET" action="{{ route('clients.index') }}" class="flex gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pesquisar por nome, e-mail ou documento"
                    class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                           placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                />
            </div>
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>

        <x-table :headers="['Nome', 'E-mail', 'Telefone', 'Documento', 'Contratos', 'Status', '']">
            @forelse ($clients as $client)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $client->name }}</td>
                    <td class="px-4 py-3">{{ $client->email }}</td>
                    <td class="px-4 py-3">{{ $client->phone }}</td>
                    <td class="px-4 py-3">{{ $client->document }}</td>
                    <td class="px-4 py-3">{{ $client->contracts_count }}</td>
                    <td class="px-4 py-3">
                        @if ($client->active)
                            <x-badge variant="success">Ativo</x-badge>
                        @else
                            <x-badge variant="neutral">Inativo</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-row-actions
                            :edit-url="route('clients.edit', $client)"
                            :delete-url="route('clients.destroy', $client)"
                            confirm="Excluir o cliente {{ $client->name }}?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Nenhum cliente encontrado.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $clients->links() }}
        </div>
    </div>
</x-layouts.app>
