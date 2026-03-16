<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Enums\RoleSquadTypeEnum;
use App\Models\Soldier;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// SoldiersManagerTest

it('check properties in livewire component', function () {
    $clan = new_clan($this->user);

    Livewire::actingAs($this->user)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertSet('name', '')
        ->assertSet('role', null)
        ->assertSet('observation', null)
        ->assertSet('bulkNames', null);
});

it('only clan_owner and clan_helper can access to add soldiers', function () {
    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');
    $otherUser = new_user();

    $clan = new_clan($owner);
    $clan->members()->attach($helper->id, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertSet('name', '')
        ->assertSet('role', null)
        ->assertSet('observation', null);

    Livewire::actingAs($helper)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertSet('name', '')
        ->assertSet('role', null)
        ->assertSet('observation', null);

    Livewire::actingAs($otherUser)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertStatus(403);
});

it('creates a soldier with optional role and observation', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->set('role', RoleSquadTypeEnum::Rifleman->value)
        ->set('observation', 'Test observation')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $soldier = $clan->soldiers()->first();
    expect($soldier->name)->toBe('alpha');
    expect($soldier->role)->toBe(RoleSquadTypeEnum::Rifleman);
    expect($soldier->observation)->toBe('Test observation');
});

it('validates required soldier name', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('validates squad role enum when provided', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->set('role', 'invalid_role')
        ->call('save')
        ->assertHasErrors(['role']);
});

it('prevents duplicate soldier names in the same clan', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    Soldier::factory()->forClan($clan)->create(['name' => 'Alpha']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('allows duplicate soldier names in different clans', function () {
    $owner1 = new_user(role: 'clan_owner');
    $owner2 = new_user(role: 'clan_owner');
    $clan1 = new_clan($owner1);
    $clan2 = new_clan($owner2);
    Soldier::factory()->forClan($clan1)->create(['name' => 'Alpha']);

    Livewire::actingAs($owner2)
        ->test('system::clans.soldiers-manager', ['clan' => $clan2])
        ->set('name', 'Alpha')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan2->soldiers()->count())->toBe(1);
});

it('creates many soldiers from comma or newline separated input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('bulkNames', "Alpha, Beta\nGamma")
        ->set('manySoldiers', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(3);
    expect($clan->soldiers()->pluck('name')->sort()->values()->toArray())
        ->toBe(['alpha', 'beta', 'gamma']);
});

it('stores null role and observation for bulk created soldiers', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha, Beta')
        ->call('save');

    expect($clan->soldiers()->count())->toBe(2);
    expect($clan->soldiers()->whereNull('role')->whereNull('observation')->count())->toBe(2);
});

it('ignores empty values in bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha,,Beta,')
        ->call('save');

    expect($clan->soldiers()->count())->toBe(2);
});

it('ignores duplicated names inside the same bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha, Alpha, Beta')
        ->call('save');

    expect($clan->soldiers()->count())->toBe(2);
});

it('forbids managing soldiers for a clan the user does not own', function () {
    $owner1 = new_user(role: 'clan_owner');
    $owner2 = new_user(role: 'clan_owner');
    $clan1 = new_clan($owner1);
    new_clan($owner2); // owner2 has their own clan, but shouldn't access clan1

    Livewire::actingAs($owner2)
        ->test('system::clans.soldiers-manager', ['clan' => $clan1])
        ->assertStatus(403);
});

it('the soldier name must be saved in lowercase', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $soldier = $clan->soldiers()->first();
    expect($soldier->name)->toBe('alpha');
});

it('the soldier name must be saved without accents', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Álphá')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $soldier = $clan->soldiers()->first();
    expect($soldier->name)->toBe('alpha');
});

it('can edit a soldier', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    $soldier = Soldier::factory()->forClan($clan)->create(['name' => 'Alpha', 'role' => RoleSquadTypeEnum::Rifleman, 'observation' => 'Initial observation']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->call('showEditSoldier', $soldier->id)
        ->set('soldier_name', 'Bravo')
        ->set('soldier_role', RoleSquadTypeEnum::Medic->value)
        ->set('soldier_observation', 'Updated observation')
        ->call('editSoldier')
        ->assertHasNoErrors();

    $soldier->refresh();
    expect($soldier->name)->toBe('bravo');
    expect($soldier->role)->toBe(RoleSquadTypeEnum::Medic);
    expect($soldier->observation)->toBe('Updated observation');
});

it('can delete a soldier', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    $soldier = Soldier::factory()->forClan($clan)->create(['name' => 'Alpha', 'role' => RoleSquadTypeEnum::Rifleman, 'observation' => 'Initial observation']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->call('showDeleteSoldier', $soldier->id)
        ->set('current_name', 'Alpha')
        ->call('deleteSoldier')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(0);
});
