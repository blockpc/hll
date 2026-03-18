<?php

use App\Enums\FactionTypeEnum;
use App\Models\Clan;
use App\Models\Map;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses()->group('roster-edit');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = Clan::factory()->withOwner($this->owner)->withHelper($this->helper)->create();
});

it('allows a clan owner to access the edit page for a roster in their clan', function () {
    $roster = new_roster($this->clan);

    $this->actingAs($this->owner)
        ->get(route('rosters.edit', ['clan' => $this->clan->slug, 'roster' => $roster->slug]))
        ->assertOk();
});

it('forbids a clan owner from accessing the edit page for a roster in another clan', function () {
    $otherClan = new_clan(new_user());
    $roster = new_roster($otherClan);

    $this->actingAs($this->owner)
        ->get(route('rosters.edit', ['clan' => $otherClan->slug, 'roster' => $roster->slug]))
        ->assertForbidden();
});

it('resolves roster route binding by clan and slug', function () {
    $sharedSlug = 'shared-roster';
    $rosterInCurrentClan = new_roster($this->clan, ['slug' => $sharedSlug]);
    $otherClan = new_clan(new_user());
    new_roster($otherClan, ['slug' => $sharedSlug]);

    $this->actingAs($this->owner)
        ->get(route('rosters.edit', ['clan' => $this->clan->slug, 'roster' => $sharedSlug]))
        ->assertOk()
        ->assertSee($rosterInCurrentClan->name);
});

it('allows a clan owner to update a roster in their clan', function () {
    $roster = new_roster($this->clan);
    $map = Map::query()->firstOrFail();
    $centralPoint = $map->centralPoints()->firstOrFail();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-edit', ['clan' => $this->clan, 'roster' => $roster])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'name' => 'Roster Uno',
        'slug' => $roster->slug,
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
        'image' => null,
        'is_public' => 0,
        'multiclan' => 0,
    ]);
});

it('allows a clan helper to update a roster in their clan', function () {
    $roster = new_roster($this->clan);
    $map = Map::query()->first();
    $centralPoint = $map->centralPoints()->first();

    Livewire::actingAs($this->helper)
        ->test('system::rosters.roster-edit', ['clan' => $this->clan, 'roster' => $roster])
        ->set('name', 'Roster Uno')
        ->set('map_id', $map->id)
        ->set('central_point_id', $centralPoint->id)
        ->set('faction', FactionTypeEnum::Allies)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'name' => 'Roster Uno',
        'slug' => $roster->slug,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
    ]);
});

it('does not allow updating a roster in a clan the user does not belong to', function () {
    $otherClan = new_clan(new_user());
    $otherRoster = new_roster($otherClan);
    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-edit', ['clan' => $otherClan, 'roster' => $otherRoster])
        ->assertForbidden();
});

it('preserves the roster slug when updating other fields', function () {
    $roster = new_roster($this->clan);

    Livewire::actingAs($this->helper)
        ->test('system::rosters.roster-edit', ['clan' => $this->clan, 'roster' => $roster])
        ->set('name', 'Roster Uno')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'slug' => $roster->slug,
    ]);
});

it('replaces the previous roster image when uploading a new one', function () {
    Storage::fake('public');

    $oldImagePath = UploadedFile::fake()->image('old.jpg')->store('rosters', 'public');

    $roster = new_roster($this->clan, [
        'image' => $oldImagePath,
    ]);

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-edit', ['clan' => $this->clan, 'roster' => $roster])
        ->set('image', UploadedFile::fake()->image('new.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $roster->refresh();

    Storage::disk('public')->assertExists($roster->image);
    Storage::disk('public')->assertMissing($oldImagePath);

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'image' => $roster->image,
    ]);
});
