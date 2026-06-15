<x-auth-shell title="Login">
    <div class="flex justify-center">
        <img src="{{ asset('images/logoCoinPel.png') }}" alt="COINPEL" class="h-56 w-auto object-contain" />
    </div>

    <h1 class="mt-8 text-lg font-semibold text-brand-900">Faça login:</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-4 space-y-4">
        @csrf

        <x-input name="email" type="email" placeholder="E-mail" :value="old('email')" required autofocus autocomplete="email" />
        <x-input name="password" type="password" placeholder="Senha" required autocomplete="current-password" />

        <x-button type="submit" variant="primary" class="w-full">Entrar</x-button>
    </form>
</x-auth-shell>
