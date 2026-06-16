@php($package = $package ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="name" label="Nome" :value="$package?->name" required />
    <x-input name="destination" label="Destino" :value="$package?->destination" required />
    <x-input name="duration_days" label="Duração (dias)" type="number" min="1" :value="$package?->duration_days" required />
    <x-input name="price" label="Preço (R$)" type="number" min="0.01" step="0.01" :value="$package?->price" required />
</div>

<div class="mt-5 flex flex-col gap-1.5">
    <label for="description" class="text-sm font-medium text-brand-900">
        Descrição <span class="text-gray-400">(opcional)</span>
    </label>
    <textarea
        id="description"
        name="description"
        rows="4"
        @error('description') aria-invalid="true" @enderror
        class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
               placeholder:text-gray-400 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
    >{{ old('description', $package?->description) }}</textarea>
    @error('description')
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<label class="mt-5 flex w-fit cursor-pointer items-center gap-2.5 text-sm text-brand-900">
    <input type="checkbox" name="active" value="1"
           @checked(old('active', $package?->active ?? true))
           class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500/40" />
    Pacote ativo
</label>
