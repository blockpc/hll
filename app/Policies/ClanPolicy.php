<?php

namespace App\Policies;

use App\Models\Clan;
use App\Models\User;

class ClanPolicy
{
    /**
     * Determina si el usuario puede crear un clan.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('clan_owner')) {
            return $user->ownedClan()->doesntExist();
        }

        return $user->hasPermissionTo('clans.create');
    }

    /**
     * Determina si el usuario puede actualizar un clan.
     */
    public function update(User $user, Clan $clan): bool
    {
        if ($this->isOwnerOrEditor($user, $clan)) {
            return true;
        }

        return $user->hasRole('clan_helper')
            && $clan->helpers()->where('users.id', $user->id)->exists();
    }

    /**
     * Determina si el usuario puede gestionar soldados del clan.
     */
    public function manageSoldiers(User $user, Clan $clan): bool
    {
        return $this->update($user, $clan);
    }

    /**
     * Determina si el usuario puede gestionar ayudantes del clan.
     */
    public function manageHelpers(User $user, Clan $clan): bool
    {
        return $this->isOwnerOrEditor($user, $clan);
    }

    /**
     * Indica si el usuario es owner del clan o editor global autorizado.
     */
    private function isOwnerOrEditor(User $user, Clan $clan): bool
    {
        if ($user->id === $clan->owner_user_id) {
            return true;
        }

        return $user->hasPermissionTo('clans.edit')
            && ! $user->hasAnyRole(['clan_owner', 'clan_helper']);
    }
}
