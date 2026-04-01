<?php

/**
 * AddSoldiersToSquadsTest is focused on testing the functionality of adding soldiers to squads within a roster.
 */

use App\Enums\RosterTypeSquadEnum;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('hll', 'squads');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    set_carbon();

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
    $this->roster = new_roster($this->clan);
    $this->squad = new_squad($this->roster, RosterTypeSquadEnum::Infantry);
    $this->soldier = new_soldier($this->clan, $this->squad);
});

it('verifies the roster is_multiclan property is false', function () {
    expect($this->roster->is_multiclan)->toBeFalse();
});

it('byId: allows adding a clan soldier to a squad', function () {
    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldierId', $this->soldier->id)
        ->call('addSoldier');

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $this->squad->id,
        'soldier_id' => $this->soldier->id,
        'slot_number' => 1,
        'display_name' => $this->soldier->name,
    ]);
});

it('byId: does not allow adding a soldier from another clan', function () {
    $otherClan = new_clan();
    $otherSoldier = new_soldier($otherClan);

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldierId', $otherSoldier->id)
        ->call('addSoldier')
        ->assertHasErrors(['soldierId' => __('hll.squad_soldiers.soldier_not_in_clan_from_roster')]);
});

it('byId: does not allow adding a soldier already assigned to the roster', function () {
    add_soldier_to_squad($this->squad, $this->soldier);

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldierId', $this->soldier->id)
        ->call('addSoldier')
        ->assertHasErrors(['soldierId' => __('hll.squad_soldiers.soldier_already_assigned', ['name' => $this->soldier->name])]);
});

it('byId: does not allow adding more soldiers than the squad type capacity', function () {
    $capacity = $this->squad->capacity;
    for ($i = 0; $i < $capacity; $i++) {
        $soldier = new_soldier($this->clan);
        add_soldier_to_squad($this->squad, $soldier);
    }

    $extraSoldier = new_soldier($this->clan);

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldierId', $extraSoldier->id)
        ->call('addSoldier')
        ->assertHasErrors(['soldierId' => __('hll.squad_soldiers.squad_full')]);
});

it('byName: allows adding a manual soldier to a squad', function () {
    $manualName = fake()->name();

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', $manualName)
        ->call('addSoldier');

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $this->squad->id,
        'soldier_id' => null,
        'slot_number' => 2,
        'display_name' => $manualName,
    ]);
});

it('byName: allows adding many manual soldiers separated by comma', function () {
    $soldierNames = [fake()->name(), fake()->name(), fake()->name()];

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', implode(', ', $soldierNames))
        ->call('addSoldier');

    foreach ($soldierNames as $index => $soldierName) {
        $this->assertDatabaseHas('squad_soldiers', [
            'squad_id' => $this->squad->id,
            'soldier_id' => null,
            'slot_number' => $index + 2,
            'display_name' => $soldierName,
        ]);
    }
});

it('byName: ignores empty entries and trims names when adding many soldiers separated by comma', function () {
    $nameOne = fake()->name();
    $nameTwo = fake()->name();
    $nameThree = fake()->name();

    $rawInput = "{$nameOne},  {$nameTwo} , ,{$nameThree},";

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', $rawInput)
        ->call('addSoldier');

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $this->squad->id,
        'soldier_id' => null,
        'slot_number' => 2,
        'display_name' => $nameOne,
    ]);

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $this->squad->id,
        'soldier_id' => null,
        'slot_number' => 3,
        'display_name' => $nameTwo,
    ]);

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $this->squad->id,
        'soldier_id' => null,
        'slot_number' => 4,
        'display_name' => $nameThree,
    ]);

    expect($this->squad->soldiers()->count())->toBe(4);
});

it('byName: does not allow assigning the same soldier twice in the same roster', function () {
    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', $this->soldier->name)
        ->call('addSoldier')
        ->assertHasErrors(['soldiersByName' => __('hll.squad_soldiers.soldier_already_assigned', ['name' => $this->soldier->name])]);
});

it('byName: does not add bulk names that are already assigned in another squad of the same roster', function () {
    $otherSquad = new_squad($this->roster, RosterTypeSquadEnum::Armor);
    add_soldier_to_squad($otherSquad, onlyName: 'uno');
    add_soldier_to_squad($otherSquad, onlyName: 'dos');

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', 'uno,dos')
        ->call('addSoldier')
        ->assertHasErrors(['soldiersByName' => __('hll.squad_soldiers.soldier_already_assigned', ['name' => 'uno'])]);

    $this->squad->refresh();

    expect($this->squad->soldiers()->whereIn('display_name', ['uno', 'dos'])->count())->toBe(0);
});

it('byName: adds only new names when bulk input contains assigned names from the same roster', function () {
    $otherSquad = new_squad($this->roster, RosterTypeSquadEnum::Armor);
    add_soldier_to_squad($otherSquad, onlyName: 'uno');

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', 'uno,tres')
        ->call('addSoldier')
        ->assertHasNoErrors();

    $this->squad->refresh();

    expect($this->squad->soldiers()->where('display_name', 'uno')->count())->toBe(0)
        ->and($this->squad->soldiers()->where('display_name', 'tres')->count())->toBe(1);
});

it('byName: adds only new names when bulk input contains case-insensitive assigned names from the same roster', function () {
    $otherSquad = new_squad($this->roster, RosterTypeSquadEnum::Armor);
    add_soldier_to_squad($otherSquad, onlyName: 'uno');

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', 'UNO,tres')
        ->call('addSoldier')
        ->assertHasNoErrors();

    $this->squad->refresh();

    expect($this->squad->soldiers()->whereRaw('LOWER(display_name) = ?', ['uno'])->count())->toBe(0)
        ->and($this->squad->soldiers()->where('display_name', 'tres')->count())->toBe(1);
});

it('byName: does not allow adding more soldiers than the squad type capacity', function () {
    $manualName = fake()->name();
    $capacity = $this->squad->capacity;
    for ($i = 0; $i < $capacity; $i++) {
        $soldier = new_soldier($this->clan);
        add_soldier_to_squad($this->squad, $soldier);
    }

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', $manualName)
        ->call('addSoldier')
        ->assertHasErrors(['soldiersByName' => __('hll.squad_soldiers.squad_full')]);
});

it('assigns the next available slot number when adding a squad soldier', function () {
    $manualName = fake()->name();

    expect($this->squad->soldiers()->count())->toBe(1);

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', $manualName)
        ->call('addSoldier');

    $this->squad->refresh();

    expect($this->squad->soldiers()->count())->toBe(2);
    expect($this->squad->soldiers()->first()->slot_number)->toBe(1);
    expect($this->squad->soldiers()->reorder('slot_number', 'desc')->first()->slot_number)->toBe(2);
});

it('reorders slot numbers after deleting a squad soldier', function () {
    add_soldier_to_squad($this->squad, onlyName: 'dos');
    add_soldier_to_squad($this->squad, onlyName: 'tres');
    add_soldier_to_squad($this->squad, onlyName: 'cuatro');

    $this->squad->refresh();

    expect($this->squad->soldiers()->orderBy('slot_number')->pluck('display_name')->all())
        ->toBe([$this->soldier->name, 'dos', 'tres', 'cuatro']);
    expect($this->squad->soldiers()->orderBy('slot_number')->pluck('slot_number')->all())
        ->toBe([1, 2, 3, 4]);

    $soldierToDelete = $this->squad->soldiers()->where('display_name', 'dos')->firstOrFail();
    $soldierToDelete->delete();

    $this->squad->refresh();

    expect($this->squad->soldiers()->orderBy('slot_number')->pluck('display_name')->all())
        ->toBe([$this->soldier->name, 'tres', 'cuatro']);
    expect($this->squad->soldiers()->orderBy('slot_number')->pluck('slot_number')->all())
        ->toBe([1, 2, 3]);
});
