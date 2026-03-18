<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Provides authorization checks for roster operations.
 *
 * @property \App\Models\Roster $roster
 * @property \App\Models\Clan $clan
 */
trait CheckAuthorizationRostersTrait
{
    private function checkAuthorization(): void
    {
        $this->ensureClanMatches();

        abort_unless(
            $this->canUpdateRoster(),
            403,
            __('hll.clans.rosters.403')
        );
    }

    private function canUpdateRoster(): bool
    {
        $user = auth()->user();

        return $user?->can('update', $this->roster) ?? false;
    }

    private function ensureClanMatches(): void
    {
        abort_if(
            $this->roster->clan_id !== $this->clan->id,
            404,
            __('hll.clans.rosters.not_match')
        );
    }
}
