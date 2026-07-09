<?php

/**
 * AddSoldiersToClanServiceTest: Test the AddSoldiersToClanService class.
 */

use App\Services\AddSoldiersToClanService;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
});

it('can add one soldier to clan', function () {
    $service = new AddSoldiersToClanService;
    $service->for($this->clan)->names('John Doe');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(1)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(1);
});

it('can add many soldiers to clan', function () {
    $service = new AddSoldiersToClanService;
    $service->for($this->clan)->names('John Doe, Jane Smith, Bob Johnson');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('verifies skipped empty names', function () {
    $service = new AddSoldiersToClanService;
    $service->for($this->clan)->names('alpha,,beta, ,gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(1)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('verifies skipped too long names', function () {
    $service = new AddSoldiersToClanService;
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

it('verifies duplicates in the input are skipped', function () {
    $service = new AddSoldiersToClanService;
    $service->for($this->clan)->names('alpha, beta, gamma, alpha, beta');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(3)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['skippedDuplicates'])->toBe(['alpha', 'beta'])
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('verifies soldiers already in the clan are ignored', function () {
    new_soldier($this->clan, attributes: ['name' => 'beta']);

    $service = new AddSoldiersToClanService;
    $service->for($this->clan)->names('alpha, beta, gamma');
    $result = $service->saveBulk();

    expect($result['created'])->toBe(2)
        ->and($result['skippedEmpty'])->toBe(0)
        ->and($result['skippedTooLong'])->toBeEmpty()
        ->and($result['duplicatesIgnored'])->toBe(['beta'])
        ->and($this->clan->soldiers()->count())->toBe(3);
});

it('can add one soldier with saveSingle', function () {
    $service = new AddSoldiersToClanService;
    $result = $service->for($this->clan)->saveSingle('John Doe');

    expect($result['created'])->toBe(1)
        ->and($result['duplicatesIgnored'])->toBeEmpty()
        ->and($this->clan->soldiers()->count())->toBe(1);
});

it('saveSingle returns duplicate when soldier already exists', function () {
    new_soldier($this->clan, attributes: ['name' => 'John Doe']);

    $service = new AddSoldiersToClanService;
    $result = $service->for($this->clan)->saveSingle('John Doe');

    expect($result['created'])->toBe(0)
        ->and($result['duplicatesIgnored'])->toBe(['John Doe'])
        ->and($this->clan->soldiers()->count())->toBe(1);
});
