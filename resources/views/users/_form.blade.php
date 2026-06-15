@php($user = $user ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="name" label="Nome" :value="$user?->name" required />
    <x-input name="email" label="E-mail" type="email" :value="$user?->email" required />

    <x-input name="password" label="Senha" type="password" :required="! $user"
             :hint="$user ? 'Deixe em branco para manter a senha atual.' : 'Mínimo 8 caracteres.'"
             autocomplete="new-password" />
    <x-input name="password_confirmation" label="Confirmar senha" type="password" :required="! $user"
             autocomplete="new-password" />
</div>
