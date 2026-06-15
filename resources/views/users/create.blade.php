<x-layouts.app title="Novo usuário" heading="Usuários">
    <x-drawer title="Novo usuário" :close-url="route('users.index')">
        <form method="POST" action="{{ route('users.store') }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            <div class="flex-1 overflow-y-auto px-6 py-5">
                <p class="mb-5 text-sm text-gray-500">
                    O usuário deverá trocar a senha provisória no primeiro acesso.
                </p>
                @include('users._form')
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Finalizar cadastro</x-button>
                <x-button :href="route('users.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
