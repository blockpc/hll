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

    $clan = Clan::factory()->create([
        'owner_user_id' => $this->user->id,
    ]);

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
