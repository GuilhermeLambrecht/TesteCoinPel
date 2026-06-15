<x-layouts.app title="Viagens" heading="Viagens">
    <x-slot:actions>
        <x-button :href="route('trips.create')" variant="primary">+ Adicionar viagem</x-button>
    </x-slot:actions>

    <div class="space-y-4">
        {{-- Busca por origem/destino + filtro por data de partida --}}
        <form method="GET" action="{{ route('trips.index') }}" class="flex flex-wrap gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pesquisar por origem ou destino"
                    class="w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                           placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                />
            </div>
            <input
                type="date"
                name="date"
                value="{{ $date }}"
                aria-label="Partir a partir de"
                class="rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900
                       focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
            />
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>

        <x-table :headers="['Status', 'Trajeto', 'Partida', 'Chegada', 'Veículo', 'Motorista', '']">
            @forelse ($trips as $trip)
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <x-badge :variant="$trip->status->badgeVariant()">{{ $trip->status->label() }}</x-badge>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="font-medium">{{ $trip->origin }}</span>
                        <span class="mx-1 text-gray-400">›</span>
                        <span>{{ $trip->destination }}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $trip->departure_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $trip->arrival_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $trip->vehicle?->plate }}</td>
                    <td class="px-4 py-3">{{ $trip->driver?->name }}</td>
                    <td class="px-4 py-3">
                        <x-row-actions
                            :edit-url="route('trips.edit', $trip)"
                            :delete-url="route('trips.destroy', $trip)"
                            confirm="Excluir a viagem {{ $trip->origin }} → {{ $trip->destination }}?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Nenhuma viagem encontrada.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $trips->links() }}
        </div>
    </div>
</x-layouts.app>
