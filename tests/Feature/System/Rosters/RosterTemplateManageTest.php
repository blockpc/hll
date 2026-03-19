<?php

use App\Models\Clan;
use App\Models\Soldier;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

/**
 * RosterTemplateManageTest is focused on access control for the roster template management page.
 * We verify that only authorized users can access the page for rosters in their own clan,
 * and that unauthorized users are properly forbidden from accessing it.
 */
uses()->group('hll', 'rosters');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = Clan::factory()->withOwner($this->owner)->withHelper($this->helper)->create();
});

it('allows a clan owner to access the manage page for a roster in their clan', function () {
    $roster = new_roster($this->clan);

    $this->actingAs($this->owner)
        ->get(route('rosters.template', ['clan' => $this->clan->slug, 'roster' => $roster->uuid]))
        ->assertOk();
});

it('allows a clan helper to access the manage page for a roster in their clan', function () {
    $roster = new_roster($this->clan);

    $this->actingAs($this->helper)
        ->get(route('rosters.template', ['clan' => $this->clan->slug, 'roster' => $roster->uuid]))
        ->assertOk();
});

it('forbids a clan owner from mounting the manage component for a roster in another clan', function () {
    $otherClan = new_clan(new_user());
    $roster = new_roster($otherClan);

    Livewire::withoutLazyLoading();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-template', ['clan' => $otherClan, 'roster' => $roster])
        ->assertForbidden();
});

it('filters soldiers by the search term in the roster template component', function () {
    Livewire::withoutLazyLoading();

    $roster = new_roster($this->clan);

    Soldier::factory()->forClan($this->clan)->create(['name' => 'Alpha']);
    Soldier::factory()->forClan($this->clan)->create(['name' => 'Bravo']);
    Soldier::factory()->forClan($this->clan)->create(['name' => 'Charlie']);

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-template-manage', ['clan' => $this->clan, 'roster' => $roster])
        ->set('searchSoldier', 'brav')
        ->assertSee('Bravo')
        ->assertDontSee('Alpha')
        ->assertDontSee('Charlie');
});
