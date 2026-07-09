<?php

use App\Enums\RosterTypeSquadEnum;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('hll', 'squads');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
    $this->roster = new_roster($this->clan);
});

// SquadsManagerTest

it('allows authorized users to create a squad in a roster from their clan', function ($userProperty) {
    Livewire::actingAs($this->$userProperty)
        ->test('system::squads.squad-create', ['roster' => $this->roster])
        ->set('name', 'Squad 1')
        ->set('alias', 'S1')
        ->set('roster_type_squad', RosterTypeSquadEnum::Infantry->value)
        ->set('pos_x', 100)
        ->set('pos_y', 100)
        ->set('z_index', 1)
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('squads', [
        'roster_id' => $this->roster->id,
        'name' => 'Squad 1',
        'alias' => 'S1',
        'roster_type_squad' => RosterTypeSquadEnum::Infantry->value,
        'pos_x' => 100,
        'pos_y' => 100,
        'z_index' => 1,
    ]);
})->with([
    'clan owner' => ['owner'],
    'clan helper' => ['helper'],
]);

it('does not allow creating a squad in a roster from another clan', function () {
    $anotherOwner = new_user(role: 'clan_owner');
    $anotherClan = new_clan($anotherOwner);
    $anotherRoster = new_roster($anotherClan);

    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create', ['roster' => $anotherRoster])
        ->assertForbidden();
});

it('uses default values for pos_x, pos_y, and z_index when not provided', function () {
    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create', ['roster' => $this->roster])
        ->set('name', 'Squad 1')
        ->set('alias', 'S1')
        ->set('roster_type_squad', RosterTypeSquadEnum::Infantry->value)
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('squads', [
        'roster_id' => $this->roster->id,
        'name' => 'Squad 1',
        'alias' => 'S1',
        'roster_type_squad' => RosterTypeSquadEnum::Infantry->value,
        'pos_x' => 0,
        'pos_y' => 0,
        'z_index' => 1,
    ]);
});

it('validates that roster type is a valid enum value', function () {
    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create', ['roster' => $this->roster])
        ->set('name', 'Squad 1')
        ->set('alias', 'S1')
        ->set('roster_type_squad', 'invalid_value')
        ->call('save')
        ->assertHasErrors(['roster_type_squad']);
});

it('does not allow duplicate alias within the same roster', function () {
    $anotherSquad = new_squad($this->roster, attributes: [
        'name' => 'Squad 1',
        'alias' => 'S1',
        'roster_type_squad' => RosterTypeSquadEnum::Infantry->value,
    ]);

    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create', ['roster' => $this->roster])
        ->set('name', 'Squad 2')
        ->set('alias', 'S1')
        ->set('roster_type_squad', RosterTypeSquadEnum::Infantry->value)
        ->call('save')
        ->assertHasErrors(['alias']);
});

it('allows duplicate alias for different rosters', function () {
    new_squad($this->roster, attributes: [
        'name' => 'Squad 1',
        'alias' => 'S1',
        'roster_type_squad' => RosterTypeSquadEnum::Infantry->value,
    ]);

    $anotherRoster = new_roster($this->clan);

    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create', ['roster' => $anotherRoster])
        ->set('name', 'Squad 2')
        ->set('alias', 'S1')
        ->set('roster_type_squad', RosterTypeSquadEnum::Infantry->value)
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('squads', [
        'roster_id' => $anotherRoster->id,
        'name' => 'Squad 2',
        'alias' => 'S1',
        'roster_type_squad' => RosterTypeSquadEnum::Infantry->value,
    ]);

    assertDatabaseHas('squads', [
        'roster_id' => $this->roster->id,
        'name' => 'Squad 1',
        'alias' => 'S1',
        'roster_type_squad' => RosterTypeSquadEnum::Infantry->value,
    ]);
});
