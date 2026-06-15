@php($driver = $driver ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="name" label="Nome" :value="$driver?->name" required />
    <x-input name="cpf" label="CPF" :value="$driver?->cpf" required />
    <x-input name="cnh" label="CNH" :value="$driver?->cnh" required />
    <x-input name="cnh_category" label="Categoria da CNH" :value="$driver?->cnh_category" required />
    <x-input name="cnh_expiration" label="Validade da CNH" type="date" :value="$driver?->cnh_expiration?->format('Y-m-d')" required />
    <x-input name="phone" label="Telefone" :value="$driver?->phone" required />
</div>

<div class="mt-5">
    <label for="photo" class="mb-1.5 block text-sm font-medium text-brand-900">Foto de perfil <span class="text-gray-400">(opcional)</span></label>
    <div class="flex items-center gap-4">
        <x-avatar :src="$driver?->photo_url" :name="$driver?->name ?? ''" size="h-16 w-16" class="shrink-0" />
        <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm text-brand-900 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100" />
    </div>
    <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WEBP, até 2&nbsp;MB.</p>
    @error('photo')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<label class="mt-5 flex w-fit cursor-pointer items-center gap-2.5 text-sm text-brand-900">
    <input type="checkbox" name="active" value="1"
           @checked(old('active', $driver?->active ?? true))
           class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500/40" />
    Motorista ativo
</label>
