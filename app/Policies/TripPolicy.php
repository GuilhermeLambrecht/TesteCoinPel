<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    /**
     * Qualquer administrador autenticado gerencia as viagens (RF01).
     * As rotas já exigem autenticação; a policy formaliza a autorização.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Trip $trip): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Trip $trip): bool
    {
        return true;
    }

    public function delete(User $user, Trip $trip): bool
    {
        return true;
    }
}
