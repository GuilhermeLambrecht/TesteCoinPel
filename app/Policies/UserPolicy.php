<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Qualquer administrador autenticado gerencia os usuários (RF04).
     * A proteção contra auto-exclusão fica no controller (mensagem amigável).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $model): bool
    {
        return true;
    }

    public function delete(User $user, User $model): bool
    {
        return true;
    }
}
