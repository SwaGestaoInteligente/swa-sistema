<?php

namespace App\Policies;

use App\Models\Anexo;
use App\Models\User;

class AnexoPolicy
{
    public function view(User $user, Anexo $anexo): bool
    {
        return $user->canAccessCondominio((string) $anexo->condominio_id);
    }

    public function delete(User $user, Anexo $anexo): bool
    {
        if (! $user->canAccessCondominio((string) $anexo->condominio_id)) {
            return false;
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        return in_array($user->roleOnCondominio((string) $anexo->condominio_id), ['admin', 'sindico', 'vistoriador'], true);
    }
}
