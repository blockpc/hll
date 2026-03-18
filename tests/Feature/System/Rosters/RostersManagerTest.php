<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Models\Roster;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;

uses()->group('hll', 'rosters');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);
    $this->user = new_user();
});

// RostersManagerTest

it('other auth users can not access to roster table', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('rosters.table'));
    $response->assertForbidden();
});

it('only owners of clan can access to roster table component', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    new_clan($owner);

    $this->actingAs($owner);

    $response = $this->get(route('rosters.table'));
    $response->assertOk();
});

it('sudo can access roster table with any clan filter', function () {
    $sudo = new_user();
    $sudo->assignRole('sudo');

    $clan = new_clan(new_user());

    $this->actingAs($sudo)
        ->get(route('rosters.table', ['clan' => $clan->slug]))
        ->assertOk();
});

it('user with super admin permission can access roster table with any clan filter', function () {
    $superAdmin = new_user();
    $superAdmin->givePermissionTo('super admin');

    $clan = new_clan(new_user());

    $this->actingAs($superAdmin)
        ->get(route('rosters.table', ['clan' => $clan->slug]))
        ->assertOk();
});

it('only helpers of clan can access to roster table component', function () {
    $owner = new_user();
    $clan = new_clan($owner);

    $helper = new_user();
    $helper->assignRole('clan_helper');
    $clan->members()->attach($helper, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    $this->actingAs($helper);

    $response = $this->get(route('rosters.table'));
    $response->assertOk();
});

it('clan owner can not access roster table filtered by another clan', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $ownedClan = new_clan($owner);

    $otherClan = new_clan(new_user());

    expect($ownedClan->id)->not()->toBe($otherClan->id);

    $this->actingAs($owner)
        ->get(route('rosters.table', ['clan' => $otherClan->slug]))
        ->assertForbidden();
});

it('clan helper can access roster table filtered by member clan', function () {
    $owner = new_user();
    $clan = new_clan($owner);

    $helper = new_user();
    $helper->assignRole('clan_helper');
    $clan->members()->attach($helper, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    $this->actingAs($helper)
        ->get(route('rosters.table', ['clan' => $clan->slug]))
        ->assertOk();
});

it('clan helper can not access roster table filtered by clan where is not member', function () {
    $helper = new_user();
    $helper->assignRole('clan_helper');

    $clan = new_clan(new_user());

    $this->actingAs($helper)
        ->get(route('rosters.table', ['clan' => $clan->slug]))
        ->assertForbidden();
});

it('creates a roster for the owners clan', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $clan = new_clan($owner);

    expect($owner->can('create', [Roster::class, $clan]))->toBeTrue();

    $roster = Roster::factory()->for($clan)->create();

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'clan_id' => $clan->id,
    ]);
});

it('does not allow duplicate name in the same clan', function () {
    $owner = new_user();
    $clan = new_clan($owner);
    $name = fake()->words(2, true);

    Roster::factory()->for($clan)->create([
        'name' => $name,
    ]);

    expect(function () use ($clan, $name) {
        Roster::factory()->for($clan)->create([
            'name' => $name,
        ]);
    })->toThrow(QueryException::class);
});

it('allows duplicate name in different clans', function () {
    $ownerA = new_user();
    $ownerB = new_user();
    $clanA = new_clan($ownerA);
    $clanB = new_clan($ownerB);
    $name = fake()->words(2, true);

    $rosterA = Roster::factory()->for($clanA)->create([
        'name' => $name,
    ]);

    $rosterB = Roster::factory()->for($clanB)->create([
        'name' => $name,
    ]);

    expect($rosterA->name)->toBe($name);
    expect($rosterB->name)->toBe($name);
    expect($rosterA->clan_id)->not()->toBe($rosterB->clan_id);
});

it('forbids creating a roster for a clan the user does not own', function () {
    $ownerA = new_user();
    $ownerA->assignRole('clan_owner');
    $clanA = new_clan($ownerA);

    $ownerB = new_user();
    $clanB = new_clan($ownerB);

    expect($ownerA->can('create', [Roster::class, $clanA]))->toBeTrue();
    expect($ownerA->can('create', [Roster::class, $clanB]))->toBeFalse();
});

it('defaults is_public and multiclan to false', function () {
    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->for($clan)->create();

    expect($roster->is_public)->toBeFalse();
    expect($roster->multiclan)->toBeFalse();
});

it('allows creating a public roster', function () {
    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->for($clan)->create([
        'is_public' => true,
    ]);

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'is_public' => true,
    ]);
});

it('redirects guest to login for public roster table URL', function () {
    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->for($clan)->create([
        'is_public' => true,
    ]);

    $response = $this->get(route('rosters.table', ['clan' => $clan->alias, 'roster' => $roster->uuid]));

    $response->assertRedirect(route('login'));
});

it('redirects guest to login for private roster table URL', function () {
    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->for($clan)->create([
        'is_public' => false,
    ]);

    $response = $this->get(route('rosters.table', ['clan' => $clan->alias, 'roster' => $roster->uuid]));

    $response->assertRedirect(route('login'));
});

it('stores multiclan flag correctly', function () {
    $owner = new_user();
    $clan = new_clan($owner);
    $roster = Roster::factory()->for($clan)->create([
        'multiclan' => true,
    ]);

    expect($roster->multiclan)->toBeTrue();
});

it('shows edit url for rosters in users clan', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');
    $clan = new_clan($owner, ['slug' => 'clan-a']);
    $roster = Roster::factory()->for($clan)->create();

    $response = $this->actingAs($owner)->get(route('rosters.table'));

    $response->assertOk();
    $response->assertSee(route('rosters.edit', [$clan->slug, $roster->uuid]), false);
});

it('does not show rosters from other clans', function () {
    $owner = new_user();
    $owner->assignRole('clan_owner');

    $otherOwner = new_user();
    $otherClan = new_clan($otherOwner, ['slug' => 'clan-b']);
    $otherRoster = Roster::factory()->for($otherClan)->create();

    $response = $this->actingAs($owner)->get(route('rosters.table'));

    $response->assertOk();
    $response->assertDontSee(route('rosters.edit', [$otherClan->slug, $otherRoster->uuid]), false);
    $response->assertDontSee($otherRoster->name);
});
