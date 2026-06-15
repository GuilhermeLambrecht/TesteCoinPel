@php($trip = $trip ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="origin" label="Origem" :value="$trip?->origin" required />
    <x-input name="destination" label="Destino" :value="$trip?->destination" required />

    <x-input name="departure_at" label="Partida" type="datetime-local"
             :value="$trip?->departure_at?->format('Y-m-d\TH:i')" required />
    <x-input name="arrival_at" label="Chegada" type="datetime-local"
             :value="$trip?->arrival_at?->format('Y-m-d\TH:i')" required />

    <x-select name="vehicle_id" label="Veículo" :options="$vehicles"
              :selected="$trip?->vehicle_id" placeholder="Selecione um veículo" required />
    <x-select name="driver_id" label="Motorista" :options="$drivers"
              :selected="$trip?->driver_id" placeholder="Selecione um motorista" required />

    <x-select name="status" label="Status" :options="$statuses"
              :selected="$trip?->status?->value ?? \App\Enums\TripStatus::Agendada->value" required />
</div>
