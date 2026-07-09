<?php

use App\Enums\RosterTypeSquadEnum;
use App\Services\CreateCommanderSquadService;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll', 'squads', 'services');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->clan = new_clan($this->owner);
    $this->roster = new_roster($this->clan);
});

it('creates commander squad and assigns selected soldier in slot one', function () {
    $service = new CreateCommanderSquadService;
    $soldier = new_soldier($this->clan, attributes: ['name' => 'Captain Price']);

    $squad = $service->create($this->roster, $soldier->id, 'Comandante', 'cmte');

    expect($squad->roster_type_squad)->toBe(RosterTypeSquadEnum::Commander)
        ->and($this->roster->commandSquads()->count())->toBe(1)
        ->and($squad->soldiers()->count())->toBe(1)
        ->and($squad->soldiers()->first()?->slot_number)->toBe(1)
        ->and($squad->soldiers()->first()?->display_name)->toBe('Captain Price')
        ->and($squad->soldiers()->first()?->soldier_id)->toBe($soldier->id);
});

it('throws when commander squad already exists in roster', function () {
    $service = new CreateCommanderSquadService;
    $firstSoldier = new_soldier($this->clan);
    $secondSoldier = new_soldier($this->clan);

    $service->create($this->roster, $firstSoldier->id, 'Comandante', 'cmte');

    expect(fn () => $service->create($this->roster, $secondSoldier->id, 'Comandante 2', 'cm2'))
        ->toThrow(DomainException::class, __('hll.squads.squad_command.already_exists'));
});

it('throws when soldier does not belong to roster clan', function () {
    $service = new CreateCommanderSquadService;

    $anotherOwner = new_user(role: 'clan_owner');
    $anotherClan = new_clan($anotherOwner);
    $foreignSoldier = new_soldier($anotherClan);

    expect(fn () => $service->create($this->roster, $foreignSoldier->id, 'Comandante', 'cmte'))
        ->toThrow(DomainException::class, __('hll.squad_soldiers.soldier_not_in_clan_from_roster'));
});
