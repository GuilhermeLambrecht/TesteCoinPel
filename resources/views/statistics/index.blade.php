<x-layouts.app title="Estatísticas" heading="Estatísticas">
    <div class="space-y-10">
        {{-- 1. Indicadores principais (destaque) — peso visual maior. --}}
        <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-number label="Total de viagens" :value="$tripsTotal" />
            <x-stat-number label="Valor dos contratos ativos" value="R$ {{ number_format($contractsActiveValue, 2, ',', '.') }}" />
            <x-stat-number label="Contratos ativos" :value="$contractsActive" />
            <x-stat-number label="Motoristas ativos" :value="$driversActive" />
        </section>

        {{-- 2. Operação — viagens por status (proporção sobre o total). --}}
        <section class="space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Operação · Viagens por status</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tripStatuses as $status)
                    <x-stat-proportion :label="$status['label']" :value="$status['count']" :total="$tripsTotal" />
                @endforeach
            </div>
        </section>

        {{-- 3. Negócio — contratos por status + valor total dos ativos. --}}
        <section class="space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Negócio · Contratos por status</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($contractStatuses as $status)
                    <x-stat-proportion :label="$status['label']" :value="$status['count']" :total="$contractsTotal" />
                @endforeach
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-xs font-medium text-gray-500">Valor total dos contratos ativos</p>
                <p class="mt-1 text-2xl font-semibold text-brand-700">R$ {{ number_format($contractsActiveValue, 2, ',', '.') }}</p>
            </div>
        </section>

        {{-- 4. Frota e equipe — ativos sobre o total. --}}
        <section class="space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Frota e equipe</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <x-stat-proportion label="Veículos ativos" :value="$vehiclesActive" :total="$vehiclesTotal" />
                <x-stat-proportion label="Motoristas ativos" :value="$driversActive" :total="$driversTotal" />
                <x-stat-proportion label="Pacotes ativos" :value="$packagesActive" :total="$packagesTotal" />
            </div>
        </section>
    </div>
</x-layouts.app>
