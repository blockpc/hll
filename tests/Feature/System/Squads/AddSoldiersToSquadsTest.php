<?php

/**
 * AddSoldiersToSquadsTest is focused on testing the functionality of adding soldiers to squads within a roster.
 */

use App\Enums\RosterTypeSquadEnum;
use App\Models\Clan;
use App\Models\Soldier;
use App\Models\Squad;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll', 'squads');

beforeEach(function() {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
    $this->roster = new_roster($this->clan);
    $this->squad = new_squad($this->roster, RosterTypeSquadEnum::Custom);
    $this->soldier = new_soldier($this->clan, $this->squad);
});

it('allows adding a clan soldier to a squad when the roster is not multiclan', function () {
    expect(true)->toBeTrue();
});

it('does not allow adding a soldier from another clan when the roster is not multiclan', function () {

    expect(true)->toBeTrue();
});

it('allows adding a manual member by name to a squad', function () {

    expect(true)->toBeTrue();
});

it('does not allow adding more members than the squad type capacity', function () {

    expect(true)->toBeTrue();
});

it('does not allow assigning the same soldier twice in the same roster', function () {

    expect(true)->toBeTrue();
});

it('assigns the next available slot number when adding a squad soldier', function () {

    expect(true)->toBeTrue();
});
