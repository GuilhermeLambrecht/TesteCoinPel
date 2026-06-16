@php($client = $client ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="name" label="Nome / Razão social" :value="$client?->name" required />
    <x-input name="email" label="E-mail" type="email" :value="$client?->email" required />
    <x-input name="phone" label="Telefone" :value="$client?->phone" required />
    <x-input name="document" label="Documento (CPF/CNPJ)" :value="$client?->document" required />
</div>

<label class="mt-5 flex w-fit cursor-pointer items-center gap-2.5 text-sm text-brand-900">
    <input type="checkbox" name="active" value="1"
           @checked(old('active', $client?->active ?? true))
           class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500/40" />
    Cliente ativo
</label>
