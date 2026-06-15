<x-layouts.app title="Dashboard" heading="Dashboard">
    <div class="space-y-6">
        {{-- Saudação --}}
        <div class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-brand-900">
                Bem-vindo, {{ auth()->user()->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Visão geral do Tour Projetos.
            </p>
        </div>

        {{-- Contadores --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-stat-card label="Viagens" :value="$tripsCount">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-9 8h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2zm1-2h.01M15 18h.01" />
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Próximas viagens" :value="$upcomingTripsCount">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Veículos" :value="$vehiclesCount">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 17H3V6a1 1 0 011-1h11v12m-4 0h2m4 0h2v-5l-3-4h-5" />
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Motoristas" :value="$driversCount">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Usuários" :value="$usersCount">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </x-slot:icon>
            </x-stat-card>
        </div>

        {{-- Próximas viagens (apenas informativo) --}}
        <div class="space-y-3">
            <h3 class="text-base font-semibold text-brand-900">Próximas viagens</h3>

            <x-table :headers="['Rota', 'Partida', 'Veículo', 'Motorista']">
                @forelse ($upcomingTrips as $trip)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $trip->origin }} → {{ $trip->destination }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $trip->departure_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $trip->vehicle?->plate }}</td>
                        <td class="px-4 py-3">{{ $trip->driver?->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                            Nenhuma viagem agendada.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </div>
</x-layouts.app>
