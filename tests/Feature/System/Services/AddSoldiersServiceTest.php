<?php

/**
 * AddSoldiersServiceTest: Test the AddSoldiersService class, which is responsible for adding soldiers to a clan or squad.
 */

use App\Services\AddSoldiersService;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
    $this->squad = new_squad(new_roster($this->clan));
});

it('verifies skipped empty names', function () {
    $service = new AddSoldiersService;
    $service->for($this->clan)->names('alpha,,beta, ,gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(1)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('verifies skipped too long names', function () {
    $service = new AddSoldiersService;
    $service->for($this->clan)->names('alpha, thisnameisdefinitelytoolongtobeasoldiername, beta, anotherextremelylongnameexceedingthelimit,   ,gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(1)
        ->and($result['skippedTooLong'])->toBe([
            'thisnameisdefinitelytoolongtobeasoldiername',
            'anotherextremelylongnameexceedingthelimit',
        ])
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('verifies duplicates ignored names on clan', function () {
    new_soldier($this->clan, $this->squad, [
        'name' => 'beta',
    ]);

    $service = new AddSoldiersService;
    $service->for($this->clan)->names('alpha, beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(2)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBe(['beta'])
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('verifies duplicates ignored names', function () {
    $service = new AddSoldiersService;
    $service->for($this->clan)->names('alpha, beta, gamma, alpha, beta');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['skippedDuplicates'])->toBe(['alpha', 'beta'])
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('can add one soldier to clan', function () {
    $service = new AddSoldiersService;
    $service->for($this->clan)->names('John Doe');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(1)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(1);
});

it('can add many soldiers to clan', function () {
    $service = new AddSoldiersService;
    $service->for($this->clan)->names('John Doe, Jane Smith, Bob Johnson');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('can add one soldier to squad', function () {
    $service = new AddSoldiersService;
    $service->for($this->squad)->names('John Doe');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(1)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(1);
});

it('can add multiple soldiers to squad', function () {
    $service = new AddSoldiersService;
    $service->for($this->squad)->names('John Doe, Jane Smith, Bob Johnson');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->squad->soldiers()->count())->toBe(3);
});
