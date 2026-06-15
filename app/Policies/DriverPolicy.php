<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    /**
     * Qualquer administrador autenticado gerencia os motoristas (RF03).
     * As rotas já exigem autenticação; a policy formaliza a autorização.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Driver $driver): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Driver $driver): bool
    {
        return true;
    }

    public function delete(User $user, Driver $driver): bool
    {
        return true;
    }
}
