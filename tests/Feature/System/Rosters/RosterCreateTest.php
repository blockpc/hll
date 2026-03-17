<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Enums\FactionTypeEnum;
use App\Models\Map;
use App\Models\Roster;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses()->group('hll', 'rosters');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->clan = new_clan($this->owner);
    $this->helper = new_user(role: 'clan_helper');
    $this->clan->members()->attach($this->helper, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);
});

// RosterCreateTest

it('allows a clan owner to access to create a roster in their clan', function () {
    $this->actingAs($this->owner)
        ->get(route('rosters.create', ['clan' => $this->clan->slug]))
        ->assertOk();
});

it('forbids a clan owner to access to create a roster in other clan', function () {
    $otherClan = new_clan(new_user());
    $this->actingAs($this->owner)
        ->get(route('rosters.create', ['clan' => $otherClan->slug]))
        ->assertForbidden();
});

it('allows a clan owner to create a roster in their clan', function () {
    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'name' => 'Roster Uno',
        'slug' => Str::slug('Roster Uno'),
        'description' => null,
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
        'image' => null,
        'is_public' => false,
        'multiclan' => false,
    ]);
});

it('allows a clan helper to create a roster in their clan', function () {
    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->helper)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'name' => 'Roster Uno',
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
    ]);
});

it('does not allow creating a roster in a clan the user does not belong to', function () {
    $otherClan = new_clan(new_user());
    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-create', ['clan' => $otherClan])
        ->assertForbidden();
});

it('requires the selected central point to belong to the selected map', function () {
    $map = Map::query()->first();
    $otherMap = Map::query()->where('id', '!=', $map->id)->first();
    $centralPoint = $otherMap->centralPoints()->first();

    Livewire::actingAs($this->helper)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasErrors('central_point_id');
});

it('does not allow duplicate slug within the same clan', function () {
    Roster::factory()->for($this->clan)->create([
        'slug' => 'roster-uno',
    ]);

    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasErrors('slug');
});

it('allows the same slug in different clans', function () {
    $otherClan = new_clan(new_user());
    Roster::factory()->for($otherClan)->create([
        'slug' => 'roster-uno',
    ]);

    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('slug', 'roster-uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'name' => 'Roster Uno',
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
    ]);
});

it('generates a slug from name if not provided', function () {
    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'name' => 'Roster Uno',
        'slug' => Str::slug('Roster Uno'),
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
    ]);
});

it('normalizes the slug format before saving', function () {
    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-create', ['clan' => $this->clan])
        ->set('name', 'Roster Uno')
        ->set('slug', 'RoStEr UnO')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'name' => 'Roster Uno',
        'slug' => 'roster-uno',
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
    ]);
});
