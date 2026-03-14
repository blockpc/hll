<?php

use App\Models\Clan;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// CreateClanTest

it('does not allow guests to create clans', function () {
    $this->get(route('clans.create'))->assertRedirect(route('login'));
});

it('allows a user with the clans.create permission (not clan_owner) to access the create clan page', function () {
    $this->user->givePermissionTo('clans.create');
    $this->actingAs($this->user)->get(route('clans.create'))->assertOk();
});

it('allows a clan owner to create a clan', function () {
    $this->user->assignRole('clan_owner');
    $this->actingAs($this->user)->get(route('clans.create'))->assertOk();

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('alias', 'test')
        ->set('name', 'Test Clan')
        ->set('description', 'Descripción del clan')
        ->call('save')
        ->assertRedirect(route('clans.show', ['clan' => 'test-clan']));

    assertDatabaseHas('clans', [
        'alias' => 'test',
        'slug' => 'test-clan',
        'name' => 'Test Clan',
        'description' => 'Descripción del clan',
    ]);
});

it('clan owner can access your own clan', function () {
    $this->user->assignRole('clan_owner');

    $clan = Clan::factory()->create([
        'owner_user_id' => $this->user->id,
        'alias' => 'test',
    ]);

    $this->actingAs($this->user)->get(route('clans.show', ['clan' => $clan->slug]))->assertOk();
});

it('the slug clan must be unique', function () {
    $this->user->assignRole('clan_owner');

    $user = new_user();
    Clan::factory()->create([
        'owner_user_id' => $user->id,
        'alias' => 'test1',
        'name' => 'Test Clan',
        'slug' => 'test-clan',
    ]);

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->set('alias', 'test2')
        ->set('name', 'Test Clan')
        ->set('description', 'Descripción del clan')
        ->call('save')
        ->assertHasErrors(['slug']);
});

it('does not allow a clan owner to create a second clan', function () {
    $this->user->assignRole('clan_owner');

    Clan::factory()->create([
        'owner_user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)->get(route('clans.create'))->assertForbidden();
});

it('does not allow a clan helper to access create clan page', function () {
    $this->user->assignRole('clan_helper');
    $this->actingAs($this->user)->get(route('clans.create'))->assertForbidden();
});

it('hides the owner selector for a clan_owner', function () {
    $this->user->assignRole('clan_owner');

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-create')
        ->call('canSelectOwner')
        ->assertReturned(false);
});

it('shows the create clan button to a clan owner without a clan', function () {
    $this->user->assignRole('clan_owner');

    $this->actingAs($this->user)
        ->get(route('clans.table'))
        ->assertOk()
        ->assertSeeText(__('hll.clans.index.create'))
        ->assertSee(route('clans.create'), false);
});

it('hides the create clan button from a clan owner who already owns a clan', function () {
    $this->user->assignRole('clan_owner');

    Clan::factory()->create([
        'owner_user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('clans.table'))
        ->assertOk()
        ->assertDontSee(route('clans.create'), false);
});
