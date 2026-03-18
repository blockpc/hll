<?php

use App\Enums\ClanMembershipRoleEnum;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('hll', 'rosters');

beforeEach(function() {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->clan = new_clan($this->owner);
    $this->helper = new_user(role: 'clan_helper');
    $this->clan->members()->attach($this->helper, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);
});

it('only a clan owner can delete a roster in their clan', function () {
    $roster = new_roster($this->clan);

    $this->actingAs($this->owner)
        ->get(route('rosters.table', ['clan' => $this->clan->slug]))
        ->assertOk();

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-table', ['clan' => $this->clan])
        ->call('showDeleteRoster', $roster->id)
        ->assertHasNoErrors()
        ->set('current_name', $roster->name)
        ->call('deleteRoster')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('rosters', [
        'id' => $roster->id,
    ]);
});

it('forbids a clan helper from deleting a roster in their clan', function () {
    $roster = new_roster($this->clan);

    $this->actingAs($this->helper)
        ->get(route('rosters.table', ['clan' => $this->clan->slug]))
        ->assertOk();

    Livewire::actingAs($this->helper)
        ->test('system::rosters.roster-table', ['clan' => $this->clan])
        ->call('showDeleteRoster', $roster->id)
        ->assertForbidden();
});

it('forbids a clan owner from deleting a roster in another clan', function () {
    $otherClan = new_clan(new_user());

    Livewire::actingAs($this->owner)
        ->test('system::rosters.roster-table', ['clan' => $otherClan])
        ->assertForbidden();
});
