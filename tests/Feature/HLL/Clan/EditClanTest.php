<?php

use App\Enums\ClanMembershipRoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// EditClanTest

it('does not allow guests to edit clans', function () {
    $clan = new_clan($this->user);
    $this->get(route('clans.edit', ['clan' => $clan->slug]))->assertRedirect(route('login'));
});

it('a user with role sudo can access the edit clan page', function () {
    $this->user->assignRole('sudo');
    $owner = new_user();
    $clan = new_clan($owner);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertOk();
});

it('an user with permission super admin can edit a clan', function () {
    $this->user->givePermissionTo('super admin');
    $owner = new_user();
    $clan = new_clan($owner);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertOk();
});

it('allows a user with the clans.edit permission (not clan_owner, not clan_helper) to access the edit clan page', function () {
    $this->user->givePermissionTo('clans.edit');
    $owner = new_user();
    $clan = new_clan($owner);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertOk();
});

it('allows a clan owner to edit their own clan', function () {
    $this->user->assignRole('clan_owner');
    $clan = new_clan($this->user);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertOk();
});

it('does not allow a clan owner to edit other clans', function () {
    $this->user->assignRole('clan_owner');
    $owner = new_user();
    $clan = new_clan($owner);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertStatus(403);
});

it('a user with role clan_helper can access the edit page for their clan', function () {
    $this->user->assignRole('clan_helper');
    $owner = new_user();
    $clan = new_clan($owner);
    $clan->helpers()->attach($this->user, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertOk();
});

it('a user with role clan_helper cannot access the edit page for other clans', function () {
    $this->user->assignRole('clan_helper');
    $owner = new_user();
    $clan = new_clan($owner);
    $this->actingAs($this->user)->get(route('clans.edit', ['clan' => $clan->slug]))->assertStatus(403);
});

it('can edit clan', function () {
    Storage::fake('public');

    $this->user->assignRole('clan_owner');
    $clan = new_clan($this->user);

    $logo = UploadedFile::fake()->image('logo.png');
    $image = UploadedFile::fake()->image('image.png', 1200, 400);

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->set('alias', 'edited')
        ->set('name', 'Clan Editado')
        ->set('slug', 'clan-editado')
        ->set('description', 'Descripción del clan editado')
        ->set('discord', 'https://discord.gg/clan-editado')
        ->set('logo', $logo)
        ->set('image', $image)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clans.show', ['clan' => 'clan-editado']));

    $clan->refresh();

    expect($clan->alias)->toBe('edited');
    expect($clan->name)->toBe('Clan Editado');
    expect($clan->slug)->toBe('clan-editado');
    expect($clan->description)->toBe('Descripción del clan editado');
    expect($clan->discord_url)->toBe('https://discord.gg/clan-editado');
    expect($clan->logo)->not->toBeNull();
    expect($clan->image)->not->toBeNull();

    Storage::disk('public')->assertExists($clan->logo);
    Storage::disk('public')->assertExists($clan->image);
});

it('validates the maximum size for logo and image uploads', function () {
    Storage::fake('public');
    $this->user->assignRole('clan_owner');
    $clan = new_clan($this->user);

    $invalidLogo = UploadedFile::fake()->image('logo.png')->size(2049);
    $invalidImage = UploadedFile::fake()->image('image.png', 1200, 400)->size(2049);

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->set('logo', $invalidLogo)
        ->set('image', $invalidImage)
        ->call('save')
        ->assertHasErrors([
            'logo' => ['max'],
            'image' => ['max'],
        ]);
});

it('replaces existing logo and image files when editing a clan', function () {
    Storage::fake('public');

    $this->user->assignRole('clan_owner');

    Storage::disk('public')->put('clans/origin/old-logo.png', 'old-logo');
    Storage::disk('public')->put('clans/origin/old-image.png', 'old-image');

    $clan = new_clan($this->user, [
        'alias' => 'origin',
        'logo' => 'clans/origin/old-logo.png',
        'image' => 'clans/origin/old-image.png',
    ]);

    $newLogo = UploadedFile::fake()->image('new-logo.png');
    $newImage = UploadedFile::fake()->image('new-image.png', 1200, 400);

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->set('logo', $newLogo)
        ->set('image', $newImage)
        ->call('save')
        ->assertHasNoErrors();

    $clan->refresh();

    expect($clan->logo)->not->toBe('clans/origin/old-logo.png');
    expect($clan->image)->not->toBe('clans/origin/old-image.png');

    Storage::disk('public')->assertMissing('clans/origin/old-logo.png');
    Storage::disk('public')->assertMissing('clans/origin/old-image.png');
    Storage::disk('public')->assertExists($clan->logo);
    Storage::disk('public')->assertExists($clan->image);
});

it('allows only sudo to change the owner of an existing clan', function () {
    $sudo = new_user('sudo');
    $currentOwner = new_user();
    $newOwner = new_user();

    $clan = new_clan($currentOwner);

    Livewire::actingAs($sudo)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->call('selectUser', $newOwner->id)
        ->call('save')
        ->assertHasNoErrors();

    $clan->refresh();

    expect($clan->owner_user_id)->toBe($newOwner->id);
});

it('does not allow non-sudo users to change clan owner', function () {
    $this->user->assignRole('clan_owner');
    $clan = new_clan($this->user);
    $newOwner = new_user();

    Livewire::actingAs($this->user)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->set('selectedUserId', $newOwner->id)
        ->set('owner_user_id', $newOwner->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->fresh()->owner_user_id)->toBe($this->user->id);
});

it('prevents sudo from assigning a clan owner or helper as new owner', function (string $role): void {
    $sudo = new_user('sudo');
    $currentOwner = new_user();
    $candidate = new_user($role);
    $clan = new_clan($currentOwner);

    Livewire::actingAs($sudo)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->set('selectedUserId', $candidate->id)
        ->set('owner_user_id', $candidate->id)
        ->call('save')
        ->assertHasErrors(['owner_user_id']);

    expect($clan->fresh()->owner_user_id)->toBe($currentOwner->id);
})->with(['clan_owner', 'clan_helper']);

it('prevents sudo from assigning a user who already owns a clan', function () {
    $sudo = new_user('sudo');
    $currentOwner = new_user();
    $candidate = new_user();

    new_clan($candidate);

    $clan = new_clan($currentOwner);

    Livewire::actingAs($sudo)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->set('selectedUserId', $candidate->id)
        ->set('owner_user_id', $candidate->id)
        ->call('save')
        ->assertHasErrors(['owner_user_id']);

    expect($clan->fresh()->owner_user_id)->toBe($currentOwner->id);
});

it('shows an owner selection error immediately when sudo selects an invalid clan role user', function () {
    $sudo = new_user('sudo');
    $currentOwner = new_user();
    $candidate = new_user('clan_helper');
    $clan = new_clan($currentOwner);

    Livewire::actingAs($sudo)
        ->test('system::clans.clan-edit', ['clan' => $clan])
        ->call('selectUser', $candidate->id)
        ->assertHasErrors(['owner_user_id']);
});
