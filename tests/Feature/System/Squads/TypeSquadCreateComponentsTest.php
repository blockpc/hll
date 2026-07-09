<?php

use App\Enums\RosterTypeSquadEnum;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('hll', 'squads');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
    $this->roster = new_roster($this->clan);
});

it('creates squads from typed create component', function (RosterTypeSquadEnum $type, string $alias) {
    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create-typed', ['roster' => $this->roster, 'type' => $type])
        ->set('name', 'Squad '.mb_strtoupper($type->value))
        ->set('alias', $alias)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('squads', [
        'roster_id' => $this->roster->id,
        'name' => 'Squad '.mb_strtoupper($type->value),
        'alias' => $alias,
        'roster_type_squad' => $type->value,
    ]);
})->with([
    'infantry' => [RosterTypeSquadEnum::Infantry, 'INF1'],
    'armor' => [RosterTypeSquadEnum::Armor, 'ARM1'],
    'artillery' => [RosterTypeSquadEnum::Artillery, 'ART1'],
    'recon' => [RosterTypeSquadEnum::Recon, 'REC1'],
]);

it('marks canCreate false when the type capacity is reached', function (RosterTypeSquadEnum $type) {
    for ($i = 0; $i < $type->capacity(); $i++) {
        new_squad($this->roster, $type, ['alias' => $type->prefix().$i]);
    }

    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create-typed', ['roster' => $this->roster, 'type' => $type])
        ->assertSet('canCreate', false);
})->with([
    'infantry' => [RosterTypeSquadEnum::Infantry],
    'armor' => [RosterTypeSquadEnum::Armor],
    'artillery' => [RosterTypeSquadEnum::Artillery],
    'recon' => [RosterTypeSquadEnum::Recon],
]);

/** Pre-fills Infantry squads to capacity so canCreate is false at mount. */
it('ignores openModal call when dispatched type does not match component type', function () {
    for ($i = 0; $i < RosterTypeSquadEnum::Infantry->capacity(); $i++) {
        new_squad($this->roster, RosterTypeSquadEnum::Infantry, ['alias' => 'INF'.$i]);
    }

    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create-typed', ['roster' => $this->roster, 'type' => RosterTypeSquadEnum::Infantry])
        ->assertSet('canCreate', false)
        ->call('openModal', RosterTypeSquadEnum::Recon->value)
        ->assertSet('canCreate', false);
});

it('processes openModal call when dispatched type matches component type', function () {
    Livewire::actingAs($this->helper)
        ->test('system::squads.squad-create-typed', ['roster' => $this->roster, 'type' => RosterTypeSquadEnum::Infantry])
        ->call('openModal', RosterTypeSquadEnum::Infantry->value)
        ->assertSet('canCreate', true);
});
