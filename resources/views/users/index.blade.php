<x-layouts.app title="Usuários" heading="Usuários">
    <x-slot:actions>
        <x-button :href="route('users.create')" variant="primary">+ Adicionar usuário</x-button>
    </x-slot:actions>

    <div class="space-y-4">
        {{-- Busca por nome/e-mail --}}
        <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pesquisar por nome ou e-mail"
                    class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                           placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                />
            </div>
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>

        <x-table :headers="['Nome', 'E-mail', '1º acesso', '']">
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
