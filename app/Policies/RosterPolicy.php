<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Clan;
use App\Models\Roster;
use App\Models\User;

class RosterPolicy
{
    /**
     * Concede acceso total a roles globales antes de evaluar habilidades específicas.
     */
    public function before(User $user, string $ability): ?bool
    {
        $superAdminRole = config('permission.super_admin_role', 'sudo');

        if ($user->hasRole($superAdminRole)) {
            return true;
        }

        if ($user->hasPermissionTo('super admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determina si el usuario puede listar rosters de un clan.
     */
    public function viewAny(User $user, Clan $clan): bool
    {
        return $this->belongsToClan($user, $clan);
    }

    /**
     * Determina si el usuario puede ver un roster específico.
     */
    public function view(User $user, Roster $roster): bool
    {
        return $this->belongsToClan($user, $roster->clan);
    }

    /**
     * Determina si el usuario puede crear rosters en el clan indicado.
     */
    public function create(User $user, Clan $clan): bool
    {
        return $this->belongsToClan($user, $clan)
            && $user->hasRole(['clan_owner', 'clan_helper']);
    }

    /**
     * Determina si el usuario puede actualizar un roster.
     */
    public function update(User $user, Roster $roster): bool
    {
        return $this->belongsToClan($user, $roster->clan)
            && $user->hasRole(['clan_owner', 'clan_helper']);
    }

    /**
     * Determina si el usuario puede eliminar un roster.
     */
    public function delete(User $user, Roster $roster): bool
    {
        return $this->isClanOwner($user, $roster->clan);
    }

    /**
     * Comprueba si el usuario pertenece al clan como owner o miembro.
     */
    protected function belongsToClan(User $user, Clan $clan): bool
    {
        if ($clan->owner_user_id === $user->id) {
            return true;
        }

        return $clan->members()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * Comprueba si el usuario es owner del clan y tiene rol de clan_owner.
     */
    protected function isClanOwner(User $user, Clan $clan): bool
    {
        return $user->hasRole('clan_owner')
            && $clan->owner_user_id === $user->id;
    }
}
