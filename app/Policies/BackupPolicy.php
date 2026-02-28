<?php

namespace App\Policies;

use App\Models\Backup;
use App\Models\Condominio;
use App\Models\User;

class BackupPolicy
{
    public function viewAny(User $user, Condominio $condominio): bool
    {
        return $this->canManage($user, $condominio);
    }

    public function create(User $user, Condominio $condominio): bool
    {
        return $this->canManage($user, $condominio);
    }

    public function view(User $user, Backup $backup): bool
    {
        if ($backup->condominio_id === null) {
            return $user->isPlatformAdmin();
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $user->canAccessCondominio((string) $backup->condominio_id);
    }

    public function delete(User $user, Backup $backup): bool
    {
        if ($backup->condominio_id === null) {
            return $user->isPlatformAdmin();
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        $role = $user->roleOnCondominio((string) $backup->condominio_id);

        return in_array($role, ['admin', 'sindico'], true);
    }

    private function canManage(User $user, Condominio $condominio): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        $role = $user->roleOnCondominio((string) $condominio->id);

        return in_array($role, ['admin', 'sindico'], true);
    }
}

