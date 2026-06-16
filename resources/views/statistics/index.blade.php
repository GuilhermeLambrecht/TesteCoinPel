<x-layouts.app title="Estatísticas" heading="Estatísticas">
    <div class="space-y-8">
        {{-- Viagens --}}
        <section class="space-y-3">
            <h3 class="text-base font-semibold text-brand-900">Viagens</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                <x-stat-card label="Total de viagens" :value="$tripsTotal">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-9 8h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2zm1-2h.01M15 18h.01" />
                    </x-slot:icon>
                </x-stat-card>

                @foreach ($tripStatuses as $status)
                    <x-stat-card :label="$status['label']" :value="$status['count']" />
                @endforeach
            </div>
        </section>

        {{-- Contratos --}}
        <section class="space-y-3">
            <h3 class="text-base font-semibold text-brand-900">Contratos</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <x-stat-card label="Total de contratos" :value="$contractsTotal">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </x-slot:icon>
                </x-stat-card>

                @foreach ($contractStatuses as $status)
                    <x-stat-card :label="$status['label']" :value="$status['count']" />
                @endforeach

                <x-stat-card label="Valor dos contratos ativos" value="R$ {{ number_format($contractsActiveValue, 2, ',', '.') }}">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </x-slot:icon>
                </x-stat-card>
            </div>
        </section>

        {{-- Cadastros ativos --}}
        <section class="space-y-3">
            <h3 class="text-base font-semibold text-brand-900">Cadastros ativos</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <x-stat-card label="Veículos ativos" :value="$vehiclesActive">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 17H3V6a1 1 0 011-1h11v12m-4 0h2m4 0h2v-5l-3-4h-5" />
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card label="Motoristas ativos" :value="$driversActive">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card label="Pacotes ativos" :value="$packagesActive">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </x-slot:icon>
                </x-stat-card>
            </div>
        </section>

        {{-- Totais gerais --}}
        <section class="space-y-3">
            <h3 class="text-base font-semibold text-brand-900">Totais gerais</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card label="Veículos (total)" :value="$vehiclesTotal" />
                <x-stat-card label="Motoristas (total)" :value="$driversTotal" />
                <x-stat-card label="Pacotes (total)" :value="$packagesTotal" />
                <x-stat-card label="Usuários" :value="$usersTotal" />
            </div>
        </section>
    </div>
</x-layouts.app>
