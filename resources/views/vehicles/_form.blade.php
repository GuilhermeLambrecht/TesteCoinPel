@php($vehicle = $vehicle ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="plate" label="Placa" :value="$vehicle?->plate" required />
    <x-input name="model" label="Modelo" :value="$vehicle?->model" required />
    <x-input name="brand" label="Marca" :value="$vehicle?->brand" required />
    <x-input name="capacity" label="Capacidade" type="number" min="1" :value="$vehicle?->capacity" required />
    <x-input name="year" label="Ano" type="number" :value="$vehicle?->year" required />
</div>

<label class="mt-5 flex w-fit cursor-pointer items-center gap-2.5 text-sm text-brand-900">
    <input type="checkbox" name="active" value="1"
           @checked(old('active', $vehicle?->active ?? true))
           class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500/40" />
    Veículo ativo
</label>
