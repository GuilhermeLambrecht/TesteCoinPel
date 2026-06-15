<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TripController extends Controller
{
    /**
     * Lista as viagens em JSON (RNF03), paginadas, com vehicle e driver
     * aninhados via eager loading (sem N+1).
     *
     * Filtros opcionais e combináveis via query string: ?origin=, ?destination=,
     * ?date= (partida a partir da data). Sem filtros, retorna tudo paginado.
     * Aceita ?page=N. A resposta inclui os metadados de paginação (links + meta).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TripResource::collection(
            Trip::with(['vehicle', 'driver'])
                ->filter($request->only(['origin', 'destination', 'date']))
                ->orderByDesc('departure_at')
                ->paginate(15)
                ->withQueryString()
        );
    }
}
