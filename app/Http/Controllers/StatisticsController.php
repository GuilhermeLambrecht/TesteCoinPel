<?php

namespace App\Http\Controllers;

use App\Enums\ContractStatus;
use App\Enums\TripStatus;
use App\Models\Contract;
use App\Models\Driver;
use App\Models\Package;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Estatísticas em números: contadores e agregações simples (count/sum),
     * uma query por métrica — sem N+1, sem gráficos.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Trip::class);

        // Contagem por status em uma única query agrupada (em vez de uma por status).
        $tripsByStatus = Trip::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $contractsByStatus = Contract::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Contratos ativos: reaproveita a contagem agrupada (sem query extra).
        $contractsActive = (int) ($contractsByStatus[ContractStatus::Ativo->value] ?? 0);

        return view('statistics.index', [
            // Indicadores principais (topo).
            'tripsTotal' => Trip::count(),
            'contractsActiveValue' => (float) Contract::where('status', ContractStatus::Ativo->value)->sum('value'),
            'contractsActive' => $contractsActive,
            'driversActive' => Driver::where('active', true)->count(),

            // Operação (viagens por status) e Negócio (contratos por status).
            'tripStatuses' => $this->statusCounts(TripStatus::cases(), $tripsByStatus),
            'contractsTotal' => Contract::count(),
            'contractStatuses' => $this->statusCounts(ContractStatus::cases(), $contractsByStatus),

            // Frota e equipe (ativo / total).
            'vehiclesTotal' => Vehicle::count(),
            'vehiclesActive' => Vehicle::where('active', true)->count(),
            'driversTotal' => Driver::count(),
            'packagesTotal' => Package::count(),
            'packagesActive' => Package::where('active', true)->count(),
        ]);
    }

    /**
     * Monta [['label' => rótulo, 'count' => total], ...] para os casos de um enum,
     * preenchendo com zero os status sem registros.
     *
     * @param  array<int, TripStatus|ContractStatus>  $cases
     * @param  Collection<int|string, mixed>  $counts
     * @return array<int, array{label: string, count: int}>
     */
    private function statusCounts(array $cases, Collection $counts): array
    {
        return array_map(fn (TripStatus|ContractStatus $case): array => [
            'label' => $case->label(),
            'count' => (int) ($counts[$case->value] ?? 0),
        ], $cases);
    }
}
