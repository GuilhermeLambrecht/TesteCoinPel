<?php

namespace App\Http\Controllers;

use App\Enums\TripStatus;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripController extends Controller
{
    /**
     * Listagem paginada com eager loading (sem N+1), busca por origem/destino
     * e filtro por data de partida (a partir da data informada).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Trip::class);

        $search = trim($request->string('search')->value());
        $date = trim($request->string('date')->value());

        $trips = Trip::with(['vehicle', 'driver'])
            ->search($search)
            ->departingFrom($date !== '' ? $date : null)
            ->orderByDesc('departure_at')
            ->paginate(10)
            ->withQueryString();

        return view('trips.index', compact('trips', 'search', 'date'));
    }

    public function create(): View
    {
        $this->authorize('create', Trip::class);

        return view('trips.create', $this->formOptions());
    }

    public function store(StoreTripRequest $request): RedirectResponse
    {
        $this->authorize('create', Trip::class);

        Trip::create($request->validated());

        return redirect()->route('trips.index')
            ->with('status', 'Viagem criada com sucesso.');
    }

    public function edit(Trip $trip): View
    {
        $this->authorize('update', $trip);

        return view('trips.edit', [...$this->formOptions($trip), 'trip' => $trip]);
    }

    public function update(UpdateTripRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorize('update', $trip);

        $trip->update($request->validated());

        return redirect()->route('trips.index')
            ->with('status', 'Viagem atualizada com sucesso.');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        $this->authorize('delete', $trip);

        $trip->delete();

        return redirect()->route('trips.index')
            ->with('status', 'Viagem excluída com sucesso.');
    }

    /**
     * Opções dos selects: veículos/motoristas ativos. Na edição (quando há viagem),
     * inclui também o registro já vinculado, mesmo inativo, marcado como "(inativo)".
     *
     * @return array{vehicles: array<int, string>, drivers: array<int, string>, statuses: array<string, string>}
     */
    private function formOptions(?Trip $trip = null): array
    {
        return [
            'vehicles' => $this->vehicleOptions($trip?->vehicle_id),
            'drivers' => $this->driverOptions($trip?->driver_id),
            'statuses' => TripStatus::options(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function vehicleOptions(?int $currentId): array
    {
        return Vehicle::query()
            ->where(function ($query) use ($currentId) {
                // Ativos OU o veículo já vinculado (mesmo inativo).
                $query->where('active', true);
                if ($currentId !== null) {
                    $query->orWhere('id', $currentId);
                }
            })
            ->orderBy('plate')
            ->get()
            ->mapWithKeys(fn (Vehicle $vehicle) => [
                $vehicle->id => $vehicle->plate.' — '.$vehicle->model.($vehicle->active ? '' : ' (inativo)'),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function driverOptions(?int $currentId): array
    {
        return Driver::query()
            ->where(function ($query) use ($currentId) {
                // Ativos OU o motorista já vinculado (mesmo inativo).
                $query->where('active', true);
                if ($currentId !== null) {
                    $query->orWhere('id', $currentId);
                }
            })
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Driver $driver) => [
                $driver->id => $driver->name.($driver->active ? '' : ' (inativo)'),
            ])
            ->all();
    }
}
