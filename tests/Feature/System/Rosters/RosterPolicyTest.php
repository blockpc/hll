<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Models\Roster;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll', 'rosters');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);
    $this->user = new_user();
});

// RosterPolicyTest

it('allows sudo to delete any roster', function () {
    $this->user->assignRole('sudo');

    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->create(['clan_id' => $clan->id]);

    expect($this->user->can('delete', $roster))->toBeTrue();
});

it('allows super admin to delete any roster', function () {
    $this->user->givePermissionTo('super admin');

    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->create(['clan_id' => $clan->id]);

    expect($this->user->can('delete', $roster))->toBeTrue();
});

it('allows clan owner to create roster in own clan', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $clan = new_clan($owner);

    expect($owner->can('create', [Roster::class, $clan]))->toBeTrue();
});

it('allows clan helper to create roster in own clan', function () {
    $owner = new_user();
    $clan = new_clan($owner);

    $helper = new_user();
    $helper->assignRole('clan_helper');
    $clan->members()->attach($helper, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    expect($helper->can('create', [Roster::class, $clan]))->toBeTrue();
});

it('forbids clan helper from deleting roster', function () {
    $owner = new_user();
    $clan = new_clan($owner);

    $helper = new_user();
    $helper->assignRole('clan_helper');
    $clan->members()->attach($helper, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    $roster = Roster::factory()->create(['clan_id' => $clan->id]);

    expect($helper->can('delete', $roster))->toBeFalse();
});

it('forbids clan owner from updating roster in another clan', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $clanA = new_clan($owner);

    $clanB = new_clan(new_user());

    $rosterB = Roster::factory()->create(['clan_id' => $clanB->id]);

    expect($owner->can('update', $rosterB))->toBeFalse();
});

it('forbids users without clan roles from managing rosters', function () {
    $owner = new_user();
    $clan = new_clan($owner);

    $roster = Roster::factory()->create(['clan_id' => $clan->id]);

    expect($this->user->can('update', $roster))->toBeFalse();
    expect($this->user->can('delete', $roster))->toBeFalse();
});

it('clan_owner can view their own rosters', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $clan = new_clan($owner);

    $roster = Roster::factory()->create(['clan_id' => $clan->id]);

    expect($owner->can('view', $roster))->toBeTrue();
});

it('clan_owner can view their clan rosters', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $clan = new_clan($owner);

    expect($owner->can('viewAny', [Roster::class, $clan]))->toBeTrue();
});
