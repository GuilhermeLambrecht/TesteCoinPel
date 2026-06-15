<x-layouts.app title="Editar usuário" heading="Usuários">
    <x-drawer title="Editar usuário" :close-url="route('users.index')">
        <form method="POST" action="{{ route('users.update', $user) }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @include('users._form', ['user' => $user])
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Salvar alterações</x-button>
                <x-button :href="route('users.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
