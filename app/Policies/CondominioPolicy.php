<?php

namespace App\Policies;

use App\Models\Condominio;
use App\Models\User;

class CondominioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(User $user, Condominio $condominio): bool
    {
        return $user->canAccessCondominio((string) $condominio->id);
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Condominio $condominio): bool
    {
        if (! $user->canAccessCondominio((string) $condominio->id)) {
            return false;
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        return in_array($user->roleOnCondominio((string) $condominio->id), ['admin', 'sindico'], true);
    }

    public function delete(User $user, Condominio $condominio): bool
    {
        return $this->update($user, $condominio);
    }
}
