<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('users', 'system');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// UsersTableTest

it('un usuario sin permiso no puede acceder a la tabla de usuarios', function () {
    $this->actingAs($this->user)->get(route('users.table'))->assertForbidden();
});

it('un usuario con permiso puede acceder a la tabla de usuarios', function () {
    $this->user->givePermissionTo('users.index');

    $this->actingAs($this->user)->get(route('users.table'))->assertOk();
});

it('puede buscar users por name, email', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo('users.index');
    $userToSearch = User::factory()->create([
        'name' => 'test User',
    ]);

    Livewire::actingAs($user)
        ->test('system::users.table')
        ->set('search', 'test')
        ->assertSee('test User');
});

it('no se puede eliminar un super usuario', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo(['users.index', 'users.delete']);
    $sudo = new_user(role: 'sudo');

    Livewire::actingAs($user)
        ->test('system::users.table')
        ->call('confirmDelete', $sudo->id)
        ->assertSet('deleteModalVisible', true)
        ->set('name', $sudo->name)
        ->set('password', 'password')
        ->call('destroyUser')
        ->assertSet('deleteModalVisible', false)
        ->assertSee(__('system.users.delete.cannot_delete_sudo'));
});

it('no se puede eliminar a su propio usuario', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo(['users.index', 'users.delete']);

    Livewire::actingAs($user)
        ->test('system::users.table')
        ->call('confirmDelete', $user->id)
        ->assertSet('deleteModalVisible', true)
        ->set('name', $user->name)
        ->set('password', 'password')
        ->call('destroyUser')
        ->assertSet('deleteModalVisible', false)
        ->assertSee(__('system.users.delete.cannot_delete_yourself'));
});

it('se puede eliminar un usuario', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo(['users.index', 'users.delete']);

    $userToDelete = User::factory()->create();

    Livewire::actingAs($user)
        ->test('system::users.table')
        ->call('confirmDelete', $userToDelete->id)
        ->assertSet('deleteModalVisible', true)
        ->set('name', $userToDelete->name)
        ->set('password', 'password')
        ->call('destroyUser')
        ->assertSet('deleteModalVisible', false)
        ->assertSee(__('system.users.delete.success_message'));

    $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
});
