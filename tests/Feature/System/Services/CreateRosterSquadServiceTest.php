<?php

use App\Enums\RosterTypeSquadEnum;
use App\Exceptions\SquadCapacityExceededException;
use App\Services\CreateRosterSquadService;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll', 'squads', 'services');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->clan = new_clan($this->owner);
    $this->roster = new_roster($this->clan);
    $this->service = new CreateRosterSquadService;
});

it('creates a squad for a specific roster type', function () {
    $squad = $this->service->create(
        $this->roster,
        'Recon Team',
        'R1',
        RosterTypeSquadEnum::Recon,
    );

    expect($squad->roster_id)->toBe($this->roster->id)
        ->and($squad->name)->toBe('Recon Team')
        ->and($squad->alias)->toBe('R1')
        ->and($squad->roster_type_squad)->toBe(RosterTypeSquadEnum::Recon);
});

it('returns true when squad type is below capacity', function () {
    new_squad($this->roster, RosterTypeSquadEnum::Armor);

    expect($this->service->canCreate($this->roster, RosterTypeSquadEnum::Armor))->toBeTrue();
});

it('returns false when squad type reached capacity', function () {
    new_squad($this->roster, RosterTypeSquadEnum::Recon);
    new_squad($this->roster, RosterTypeSquadEnum::Recon);

    expect($this->service->canCreate($this->roster, RosterTypeSquadEnum::Recon))->toBeFalse();
});

it('throws SquadCapacityExceededException when creating over capacity', function () {
    new_squad($this->roster, RosterTypeSquadEnum::Recon);
    new_squad($this->roster, RosterTypeSquadEnum::Recon);

    expect(fn () => $this->service->create($this->roster, 'Extra', 'R3', RosterTypeSquadEnum::Recon))
        ->toThrow(SquadCapacityExceededException::class);
});
