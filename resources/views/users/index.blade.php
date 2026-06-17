<x-layouts.app title="Usuários" :toolbar="true">
    <div class="space-y-6">
        <x-list-toolbar
            :create-url="route('users.create')"
            create-label="+ Adicionar usuário"
            :action="route('users.index')"
            :search="$search"
            placeholder="Pesquisar por nome ou e-mail"
        />

        <x-table :flush="true" :headers="['Nome', 'E-mail', '1º acesso', '']">
            @forelse ($users as $user)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        @if ($user->must_change_password)
                            <x-badge variant="warning">Troca pendente</x-badge>
                        @else
                            <x-badge variant="success">Concluído</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-row-actions
                            :edit-url="route('users.edit', $user)"
                            :delete-url="route('users.destroy', $user)"
                            :show-delete="! $user->is(auth()->user())"
                            confirm="Excluir o usuário {{ $user->name }}?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                        Nenhum usuário encontrado.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>
