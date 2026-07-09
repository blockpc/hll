<?php

use App\Enums\RosterTypeSquadEnum;
use Database\Seeders\MapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class)->group('hll', 'squads');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(MapSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->clan = new_clan($this->owner);
    // Infantry capacity = 6, roster with plenty of room by default
    $this->roster = new_roster($this->clan, ['max_soldiers' => 20]);
    $this->squad = new_squad($this->roster, RosterTypeSquadEnum::Custom);
});

// AddSoldiersToCustomSquadTest

it('can add new lines to custom squads', function () {
    $soldierNames = [fake()->name(), fake()->name(), fake()->name()];

    Livewire::actingAs($this->owner)
        ->test('system::squads.add-soldier-to-custom-squad', ['roster' => $this->roster])
        ->call('openModal', $this->squad->id)
        ->set('soldiersByName', implode(', ', $soldierNames))
        ->call('save');

    foreach ($soldierNames as $index => $soldierName) {
        $this->assertDatabaseHas('squad_soldiers', [
            'squad_id' => $this->squad->id,
            'soldier_id' => null,
            'slot_number' => $index + 1,
            'display_name' => $soldierName,
        ]);
    }
});
