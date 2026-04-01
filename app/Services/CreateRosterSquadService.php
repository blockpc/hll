<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RosterTypeSquadEnum;
use App\Exceptions\SquadCapacityExceededException;
use App\Models\Roster;
use App\Models\Squad;

final class CreateRosterSquadService
{
    public function canCreate(Roster $roster, RosterTypeSquadEnum $type): bool
    {
        return $this->countByType($roster, $type) < $type->capacity();
    }

    public function countByType(Roster $roster, RosterTypeSquadEnum $type): int
    {
        return $roster->squads()->where('roster_type_squad', $type)->count();
    }

    /**
     * Create a new squad for the roster.
     *
     * @throws SquadCapacityExceededException when the type capacity is already reached.
     */
    public function create(Roster $roster, string $name, string $alias, RosterTypeSquadEnum $type): Squad
    {
        if (! $this->canCreate($roster, $type)) {
            throw SquadCapacityExceededException::forType($type);
        }

        return $roster->squads()->create([
            'name' => $name,
            'alias' => $alias,
            'roster_type_squad' => $type,
        ]);
    }
}
