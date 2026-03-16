<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Models\Clan;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// CreateClanHelperTest

it('allows a clan owner to create a helper for their clan', function () {
    $this->user->assignRole('clan_owner');
    $helperName = fake()->name();
    $helperEmail = fake()->unique()->safeEmail();

    $clan = new_clan($this->user);

    Livewire::actingAs($this->user)
        ->test('system::clans.helpers-manager', ['clan' => $clan])
        ->set('name', $helperName)
        ->set('email', $helperEmail)
        ->call('save')
        ->assertRedirect(route('clans.show', ['clan' => $clan->slug]));

    assertDatabaseHas('users', [
        'name' => $helperName,
        'email' => $helperEmail,
    ]);

    $createdHelper = User::query()->where('email', $helperEmail)->firstOrFail();
    assertDatabaseHas('clan_user', [
        'clan_id' => $clan->id,
        'user_id' => $createdHelper->id,
        'membership_role' => ClanMembershipRoleEnum::Helper->value,
    ]);

    $clan->refresh();

    expect($clan->helpers)->toHaveCount(1)
        ->first()
        ->name->toBe($helperName);
});

it('forbids a clan helper from creating helpers', function () {
    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');

    $clan = Clan::factory()
        ->withOwner($owner)
        ->withHelper($helper)
        ->create();

    Livewire::actingAs($helper)
        ->test('system::clans.helpers-manager', ['clan' => $clan])
        ->assertForbidden();
});

it('can update a helper member', function () {
    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');

    $clan = Clan::factory()
        ->withOwner($owner)
        ->withHelper($helper)
        ->create();

    Livewire::actingAs($owner)
        ->test('system::clans.helpers-manager', ['clan' => $clan])
        ->call('showEditModal', $helper->id)
        ->set('editingHelperName', 'Updated Helper Name')
        ->set('editingHelperEmail', $helper->email)
        ->call('editHelper')
        ->assertHasNoErrors();

    $helper->refresh();

    expect($helper->name)->toBe('Updated Helper Name');
});

it('check error update name exists', function () {

    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');
    $otheHelper = new_user(role: 'clan_helper');

    $clan = Clan::factory()
        ->withOwner($owner)
        ->withHelper($helper)
        ->create();

    Livewire::actingAs($owner)
        ->test('system::clans.helpers-manager', ['clan' => $clan])
        ->call('showEditModal', $helper->id)
        ->set('editingHelperName', 'Updated Helper Name')
        ->set('editingHelperEmail', $otheHelper->email)
        ->call('editHelper')
        ->assertHasErrors('editingHelperEmail');
});

it('can delete a member of clan', function () {

    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');

    $clan = Clan::factory()
        ->withOwner($owner)
        ->withHelper($helper)
        ->create();

    Livewire::actingAs($owner)
        ->test('system::clans.helpers-manager', ['clan' => $clan])
        ->call('showDeleteModal', $helper->id)
        ->set('current_name', $helper->name)
        ->call('deleteHelper')
        ->assertHasNoErrors();

    $clan->refresh();

    expect($clan->helpers)->toHaveCount(0);
});

it('check error current name are not equal', function () {

    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');

    $clan = Clan::factory()
        ->withOwner($owner)
        ->withHelper($helper)
        ->create();

    Livewire::actingAs($owner)
        ->test('system::clans.helpers-manager', ['clan' => $clan])
        ->call('showDeleteModal', $helper->id)
        ->set('current_name', 'WRONG NAME')
        ->call('deleteHelper')
        ->assertHasErrors('current_name');
});
