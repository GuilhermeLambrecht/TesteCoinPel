<x-auth-shell title="Crie uma nova senha">
    {{-- Card sobreposto como modal (não dispensável). A troca de senha do 1º
         acesso continua obrigatória: só avança enviando a nova senha. --}}
    <x-slot:modal>
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
            <div class="mb-6 flex justify-center">
                <img src="{{ asset('images/logoCoinPel.png') }}" alt="COINPEL" class="h-24 w-auto object-contain" />
            </div>

            <h1 class="text-lg font-semibold text-brand-900">Crie uma nova senha:</h1>
            <p class="mt-1 text-sm text-gray-500">
                No seu primeiro acesso é necessário trocar a senha provisória.
                É obrigatório que a senha tenha no mínimo 8 caracteres.
            </p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                @csrf

                <x-input name="password" type="password" label="Nova senha" required autocomplete="new-password" />
                <x-input name="password_confirmation" type="password" label="Repetir senha" required autocomplete="new-password" />

                <x-button type="submit" variant="primary" class="w-full">Confirmar</x-button>
            </form>
        </div>
    </x-slot:modal>
</x-auth-shell>
