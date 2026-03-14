<?php

use App\Models\Clan;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
    $this->user->givePermissionTo('clans.create');
});

it('allows a user with clans.create permission to access the create clan page', function () {
    $this->actingAs($this->user)
        ->get(route('clans.create'))
        ->assertOk()
        ->assertSeeText(__('hll.clans.create.form.owner_help'));
});

it('allows a user with clans.create permission to create a clan for another user without a clan', function () {
    $owner = new_user();

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('selectedUserId', $owner->id)
        ->set('alias', 'test')
        ->set('name', 'Test Clan')
        ->set('description', 'Descripción del clan')
        ->call('save')
        ->assertRedirect(route('clans.show', ['clan' => 'test-clan']));

    assertDatabaseHas('clans', [
        'owner_user_id' => $owner->id,
        'alias' => 'test',
        'slug' => 'test-clan',
    ]);

    expect($owner->fresh()->hasRole('clan_owner'))->toBeTrue();
});

it('prevents a user with clans.create permission from assigning themselves as owner', function () {
    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('owner_user_id', $this->user->id)
        ->set('alias', 'test')
        ->set('name', 'Test Clan')
        ->call('save')
        ->assertHasErrors(['owner_user_id']);
});

it('shows the owner selector for a user with clans.create permission who is not clan_owner', function () {
    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->call('canSelectOwner')
        ->assertReturned(true);
});

it('shows the owner selector for a sudo user', function () {
    $this->user->assignRole('sudo');

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->call('canSelectOwner')
        ->assertReturned(true);
});

it('shows the owner selector for a user with super admin permission', function () {
    $this->user->revokePermissionTo('clans.create');
    $this->user->givePermissionTo('super admin');

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->call('canSelectOwner')
        ->assertReturned(true);
});

it('prevents a user with clans.create permission from assigning an owner that already has a clan', function () {
    $owner = new_user();
    Clan::factory()->create(['owner_user_id' => $owner->id]);

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('owner_user_id', $owner->id)
        ->set('alias', 'test')
        ->set('name', 'Test Clan')
        ->set('description', 'Descripción del clan')
        ->call('save')
        ->assertHasErrors(['owner_user_id']);
});

it('allows assigning a user who is clan_owner and still has no clan', function () {
    $owner = new_user(role: 'clan_owner');

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('selectedUserId', $owner->id)
        ->set('alias', 'ownr')
        ->set('name', 'Owner Clan')
        ->set('description', 'Descripción del clan')
        ->call('save')
        ->assertRedirect(route('clans.show', ['clan' => 'owner-clan']));

    assertDatabaseHas('clans', [
        'owner_user_id' => $owner->id,
        'alias' => 'ownr',
        'slug' => 'owner-clan',
    ]);
});

it('requires owner_user_id when the user is not a clan_owner', function () {
    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('alias', 'test')
        ->set('name', 'Test Clan')
        ->call('save')
        ->assertHasErrors(['owner_user_id']);
});
