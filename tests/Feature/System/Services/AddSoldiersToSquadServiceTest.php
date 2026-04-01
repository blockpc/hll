<?php

/**
 * AddSoldiersToSquadServiceTest: Test the AddSoldiersToSquadService class.
 *
 * The service knows both the squad (capacity per type) and the roster (max_soldiers limit).
 */

use App\Enums\RosterTypeSquadEnum;
use App\Services\AddSoldiersToSquadService;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('hll', 'squads');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->clan = new_clan($this->owner);
    // Infantry capacity = 6, roster with plenty of room by default
    $this->roster = new_roster($this->clan, ['max_soldiers' => 20]);
    $this->squad = new_squad($this->roster, RosterTypeSquadEnum::Infantry);
});

it('can add one soldier to squad', function () {
    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('John Doe');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(1)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(1);
});

it('can add multiple soldiers to squad', function () {
    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('John Doe, Jane Smith, Bob Johnson');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(3);
});

it('verifies skipped empty names', function () {
    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('alpha,,beta, ,gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(1)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(3);
});

it('verifies skipped too long names', function () {
    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('alpha, thisnameisdefinitelytoolongtobeasoldiername, beta, anotherextremelylongnameexceedingthelimit,   ,gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(1)
        ->and($result['skippedTooLong'])->toBe([
            'thisnameisdefinitelytoolongtobeasoldiername',
            'anotherextremelylongnameexceedingthelimit',
        ])
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(3);
});

it('verifies duplicates in the input are skipped', function () {
    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('alpha, beta, gamma, alpha, beta');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['skippedDuplicates'])->toBe(['alpha', 'beta'])
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(3);
});

it('verifies soldiers already in the squad are ignored', function () {
    add_soldier_to_squad($this->squad, onlyName: 'beta');

    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('alpha, beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(2)
        ->and($result['duplicatesIgnored'])->toBe(['beta'])
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(3);
});

it('verifies soldiers already in the squad are ignored case-insensitively', function () {
    add_soldier_to_squad($this->squad, onlyName: 'beta');

    $service = new AddSoldiersToSquadService;
    $service->for($this->squad)->names('Beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(1)
        ->and($result['duplicatesIgnored'])->toBe(['Beta'])
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(2);
});

it('skips soldiers when squad capacity is reached', function () {
    // Recon squad has capacity = 2
    $reconSquad = new_squad($this->roster, RosterTypeSquadEnum::Recon);
    add_soldier_to_squad($reconSquad, onlyName: 'existing one');
    add_soldier_to_squad($reconSquad, onlyName: 'existing two');

    $service = new AddSoldiersToSquadService;
    $service->for($reconSquad)->names('alpha, beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(0)
        ->and($result['skippedFull'])->toBe(['alpha', 'beta', 'gamma'])
        ->and($reconSquad->soldiers()->count())->toBe(2);
});

it('partially fills squad up to its capacity and skips the rest', function () {
    // Recon squad has capacity = 2
    $reconSquad = new_squad($this->roster, RosterTypeSquadEnum::Recon);
    add_soldier_to_squad($reconSquad, onlyName: 'existing one');

    $service = new AddSoldiersToSquadService;
    $service->for($reconSquad)->names('alpha, beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(1)
        ->and($result['skippedFull'])->toBe(['beta', 'gamma'])
        ->and($reconSquad->soldiers()->count())->toBe(2);
});

it('skips soldiers when roster max_soldiers is reached', function () {
    $roster = new_roster($this->clan, ['max_soldiers' => 2]);
    $squad = new_squad($roster, RosterTypeSquadEnum::Infantry);
    add_soldier_to_squad($squad, onlyName: 'existing one');
    add_soldier_to_squad($squad, onlyName: 'existing two');

    $service = new AddSoldiersToSquadService;
    $service->for($squad)->names('alpha, beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(0)
        ->and($result['skippedFull'])->toBe(['alpha', 'beta', 'gamma'])
        ->and($squad->soldiers()->count())->toBe(2);
});

it('partially fills roster up to max_soldiers and skips the rest', function () {
    $roster = new_roster($this->clan, ['max_soldiers' => 3]);
    $squad = new_squad($roster, RosterTypeSquadEnum::Infantry);
    add_soldier_to_squad($squad, onlyName: 'existing one');

    $service = new AddSoldiersToSquadService;
    $service->for($squad)->names('alpha, beta, gamma, delta');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(2)
        ->and($result['skippedFull'])->toBe(['gamma', 'delta'])
        ->and($squad->soldiers()->count())->toBe(3);
});

it('can add one soldier with saveSingle', function () {
    $service = new AddSoldiersToSquadService;
    $result = $service->for($this->squad)->saveSingle('John Doe');

    expect($result['created'])->toBe(1)
        ->and($result['skippedFull'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(1);
});

it('saveSingle skips when squad is full', function () {
    $reconSquad = new_squad($this->roster, RosterTypeSquadEnum::Recon);
    add_soldier_to_squad($reconSquad, onlyName: 'existing one');
    add_soldier_to_squad($reconSquad, onlyName: 'existing two');

    $service = new AddSoldiersToSquadService;
    $result = $service->for($reconSquad)->saveSingle('alpha');

    expect($result['created'])->toBe(0)
        ->and($result['skippedFull'])->toBe(['alpha'])
        ->and($reconSquad->soldiers()->count())->toBe(2);
});
