<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    /**
     * Qualquer administrador autenticado gerencia os pacotes.
     * As rotas já exigem autenticação; a policy formaliza a autorização.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Package $package): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Package $package): bool
    {
        return true;
    }

    public function delete(User $user, Package $package): bool
    {
        return true;
    }
}
