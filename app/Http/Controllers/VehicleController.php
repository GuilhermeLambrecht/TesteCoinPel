<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Listagem paginada com busca por placa/modelo.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vehicle::class);

        $search = trim($request->string('search')->value());

        $vehicles = Vehicle::search($search)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', Vehicle::class);

        return view('vehicles.create');
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->authorize('create', Vehicle::class);

        Vehicle::create($request->validated());

        return redirect()->route('vehicles.index')
            ->with('status', 'Veículo criado com sucesso.');
    }

    public function edit(Vehicle $vehicle): View
    {
        $this->authorize('update', $vehicle);

        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('update', $vehicle);

        $vehicle->update($request->validated());

        return redirect()->route('vehicles.index')
            ->with('status', 'Veículo atualizado com sucesso.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete', $vehicle);

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('status', 'Veículo excluído com sucesso.');
    }
}
