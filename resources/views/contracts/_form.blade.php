@php($contract = $contract ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="title" label="Título" :value="$contract?->title" required />

    <x-select name="client_id" label="Cliente" :options="$clients"
              :selected="$contract?->client_id" placeholder="Selecione um cliente" required />

    <x-select name="package_id" label="Pacote" :options="$packages"
              :selected="$contract?->package_id" placeholder="Selecione um pacote" required />

    <x-input name="start_date" label="Início da vigência" type="date"
             :value="$contract?->start_date?->format('Y-m-d')" required />
    <x-input name="end_date" label="Término da vigência" type="date"
             :value="$contract?->end_date?->format('Y-m-d')" required />

    <x-input name="value" label="Valor (R$)" type="number" min="0.01" step="0.01"
             :value="$contract?->value" required />

    <x-select name="status" label="Status" :options="$statuses"
              :selected="$contract?->status?->value ?? \App\Enums\ContractStatus::Rascunho->value" required />
</div>
