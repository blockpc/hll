<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Models\Clan;
use Database\Seeders\RolesAndPermissionsSeeder;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// ClanPolicy::create

it('prevents a user without the clan_owner role from creating a clan', function () {
    expect($this->user->can('create', Clan::class))->toBeFalse();
});

it('prevents a clan_owner who already owns a clan from creating another', function () {
    $this->user->assignRole('clan_owner');
    Clan::factory()->create(['owner_user_id' => $this->user->id]);

    expect($this->user->can('create', Clan::class))->toBeFalse();
});

it('allows a clan_owner without a clan to create one', function () {
    $this->user->assignRole('clan_owner');

    expect($this->user->can('create', Clan::class))->toBeTrue();
});

// ClanPolicy::update

it('allows the clan owner to update their clan', function () {
    $clan = Clan::factory()->create(['owner_user_id' => $this->user->id]);

    expect($this->user->can('update', $clan))->toBeTrue();
});

it('prevents a non-owner from updating a clan', function () {
    $owner = new_user();
    $clan = Clan::factory()->create(['owner_user_id' => $owner->id]);

    expect($this->user->can('update', $clan))->toBeFalse();
});

it('allows a clan_helper to update the clan they belong to', function () {
    $this->user->assignRole('clan_helper');
    $owner = new_user(role: 'clan_owner');
    $clan = Clan::factory()->create(['owner_user_id' => $owner->id]);
    $clan->members()->attach($this->user->id, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    expect($this->user->can('update', $clan))->toBeTrue();
});

it('prevents a clan_helper from updating a clan they do not belong to', function () {
    $this->user->assignRole('clan_helper');
    $owner = new_user(role: 'clan_owner');
    $clan = Clan::factory()->create(['owner_user_id' => $owner->id]);

    expect($this->user->can('update', $clan))->toBeFalse();
});
