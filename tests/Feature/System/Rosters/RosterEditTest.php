<?php

use App\Enums\FactionTypeEnum;
use App\Models\Clan;
use App\Models\Map;
use App\Models\Roster;
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
        ->get(route('rosters.edit', ['clan' => $this->clan->slug, 'roster' => $roster->uuid]))
        ->assertOk();
});

it('forbids a clan owner from accessing the edit page for a roster in another clan', function () {
    $otherClan = new_clan(new_user());
    $roster = new_roster($otherClan);

    $this->actingAs($this->owner)
        ->get(route('rosters.edit', ['clan' => $otherClan->slug, 'roster' => $roster->uuid]))
        ->assertForbidden();
});

it('resolves roster route binding by clan and uuid', function () {
    $rosterInCurrentClan = new_roster($this->clan);
    $otherClan = new_clan(new_user());
    new_roster($otherClan);

    $this->actingAs($this->owner)
        ->get(route('rosters.edit', ['clan' => $this->clan->slug, 'roster' => $rosterInCurrentClan->uuid]))
        ->assertOk()
        ->assertSee($rosterInCurrentClan->name);
});

it('resolves roster route binding when clan route parameter is a slug string', function () {
    $roster = new_roster($this->clan);

    $request = request();
    $originalRouteResolver = $request->getRouteResolver();
    $resolvedRoster = null;

    try {
        $request->setRouteResolver(function (): object {
            return new class
            {
                public function parameter($key, $default = null): mixed
                {
                    if ($key === 'clan') {
                        return 'test-clan-slug';
                    }

                    return $default;
                }
            };
        });

        $this->clan->update(['slug' => 'test-clan-slug']);

        $resolvedRoster = (new Roster)
            ->resolveRouteBindingQuery(Roster::query(), $roster->uuid)
            ->first();
    } finally {
        $request->setRouteResolver($originalRouteResolver);
    }

    expect($resolvedRoster)->not->toBeNull();
    expect($resolvedRoster?->is($roster))->toBeTrue();
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
        'clan_id' => $this->clan->id,
        'map_id' => $map->id,
        'central_point_id' => $centralPoint->id,
        'faction' => FactionTypeEnum::Allies->value,
        'image' => null,
        'is_public' => 0,
        'is_multiclan' => 0,
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

it('preserves the roster uuid when updating other fields', function () {
    $roster = new_roster($this->clan);
    $uuid = $roster->uuid;

    Livewire::actingAs($this->helper)
        ->test('system::rosters.roster-edit', ['clan' => $this->clan, 'roster' => $roster])
        ->set('name', 'Roster Uno')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rosters', [
        'id' => $roster->id,
        'uuid' => $uuid,
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
