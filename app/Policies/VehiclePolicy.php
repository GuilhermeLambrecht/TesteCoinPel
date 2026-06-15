<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * Qualquer administrador autenticado gerencia os veículos (RF02).
     * As rotas já exigem autenticação; a policy formaliza a autorização.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return true;
    }
}
