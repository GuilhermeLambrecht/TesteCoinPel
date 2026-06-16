<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    /**
     * Qualquer administrador autenticado gerencia os contratos.
     * As rotas já exigem autenticação; a policy formaliza a autorização.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $contract): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contract $contract): bool
    {
        return true;
    }

    public function delete(User $user, Contract $contract): bool
    {
        return true;
    }
}
