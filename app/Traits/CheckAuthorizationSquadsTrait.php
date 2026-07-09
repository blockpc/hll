<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Provides authorization checks for roster updates.
 *
 * @property \App\Models\Roster $roster The roster instance to check authorization against
 */
trait CheckAuthorizationSquadsTrait
{
    private function checkAuthorization(): void
    {
        abort_unless(
            $this->canUpdateRoster(),
            403,
            __('hll.squads.403')
        );
    }

    private function canUpdateRoster(): bool
    {
        $user = auth()->user();

        return $user?->can('update', $this->roster) ?? false;
    }
}
