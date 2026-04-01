<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Models\Squad;
use DomainException;
use Illuminate\Support\Facades\DB;

final class CreateCommanderSquadService
{
    /**
     * Create a commander squad for the given roster.
     *
     * @throws DomainException If roster not found, commander squad already exists, or soldier not in clan.
     */
    public function create(Roster $roster, int $soldierId, string $name, ?string $alias): Squad
    {
        return DB::transaction(function () use ($roster, $soldierId, $name, $alias): Squad {
            $lockedRoster = Roster::query()->whereKey($roster->id)->lockForUpdate()->first();

            if ($lockedRoster === null) {
                throw new DomainException(__('hll.clans.rosters.template.404'));
            }

            if ($lockedRoster->commandSquads()->exists()) {
                throw new DomainException(__('hll.squads.squad_command.already_exists'));
            }

            $soldier = $lockedRoster->clan->soldiers()->whereKey($soldierId)->first();

            if ($soldier === null) {
                throw new DomainException(__('hll.squad_soldiers.soldier_not_in_clan_from_roster'));
            }

            $squad = Squad::create([
                'roster_id' => $lockedRoster->id,
                'name' => $name,
                'alias' => $alias,
                'roster_type_squad' => RosterTypeSquadEnum::Commander,
            ]);

            $squad->soldiers()->create([
                'soldier_id' => $soldier->id,
                'slot_number' => 1,
                'display_name' => $soldier->name,
            ]);

            return $squad;
        });
    }
}
