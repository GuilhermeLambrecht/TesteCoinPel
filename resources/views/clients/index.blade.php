<x-layouts.app title="Clientes" :toolbar="true">
    <div class="space-y-6">
        <x-list-toolbar
            :create-url="route('clients.create')"
            create-label="+ Adicionar cliente"
            :action="route('clients.index')"
            :search="$search"
            placeholder="Pesquisar por nome, e-mail ou documento"
        />

        {{-- Apenas Nome e E-mail na listagem. phone/document continuam no
             cadastro, na validação e na busca — só não são exibidos aqui. --}}
        <x-table :flush="true" :headers="['Nome', 'E-mail', '']">
            @forelse ($clients as $client)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $client->name }}</td>
                    <td class="px-4 py-3">{{ $client->email }}</td>
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
                    <td colspan="3" class="px-4 py-10 text-center text-gray-500">
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
