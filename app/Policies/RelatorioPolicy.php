<?php

namespace App\Policies;

use App\Models\Relatorio;
use App\Models\User;

class RelatorioPolicy
{
    public function view(User $user, Relatorio $relatorio): bool
    {
        return $user->canAccessCondominio((string) $relatorio->condominio_id);
    }

    public function email(User $user, Relatorio $relatorio): bool
    {
        if (! $user->canAccessCondominio((string) $relatorio->condominio_id)) {
            return false;
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        return in_array($user->roleOnCondominio((string) $relatorio->condominio_id), ['admin', 'sindico', 'vistoriador'], true);
    }
}
