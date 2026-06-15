<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Exibe o painel administrativo com contadores simples (uma query COUNT
     * por contador; sem N+1).
     */
    public function index(): View
    {
        return view('dashboard', [
            'tripsCount' => Trip::count(),
            'vehiclesCount' => Vehicle::count(),
            'driversCount' => Driver::count(),
            'usersCount' => User::count(),
            'upcomingTripsCount' => Trip::where('departure_at', '>', now())->count(),
            // Próximas 5 viagens, com eager loading de vehicle/driver (sem N+1).
            'upcomingTrips' => Trip::with(['vehicle', 'driver'])
                ->where('departure_at', '>', now())
                ->orderBy('departure_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
