<x-auth-shell title="Crie uma nova senha">
    {{-- 1º acesso (RF06): símbolo COINPEL acima do card (mesmo tamanho do logo da
         tela de login), card compacto abaixo, sobre o painel branco + ilustração
         (via auth-shell). A troca continua OBRIGATÓRIA e não dispensável: não há
         botão de fechar/pular e o middleware ForcePasswordChange impede acessar o
         sistema sem trocar a senha. --}}
    <div class="flex justify-center">
        <img src="{{ asset('images/coinpelSimbolo.png') }}" alt="COINPEL" class="h-56 w-auto object-contain" />
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
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
</x-auth-shell>
