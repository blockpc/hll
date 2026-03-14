<?php

namespace App\Policies;

use App\Models\Clan;
use App\Models\User;

class ClanPolicy
{
    public function create(User $user): bool
    {
        if ($user->hasRole('clan_owner')) {
            return $user->ownedClan()->doesntExist();
        }

        return $user->hasPermissionTo('clans.create');
    }

    public function update(User $user, Clan $clan): bool
    {
        if ($user->id === $clan->owner_user_id) {
            return true;
        }

        return $user->hasRole('clan_helper')
            && $clan->helpers()->where('users.id', $user->id)->exists();
    }
}
