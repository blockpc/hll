<?php

use App\Models\Permission;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('permissions', 'system');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

// PermissionsTableTest

it('un usuario con role sudo puede ver el permiso super admin', function () {
    $sudo = new_user(role: 'sudo');
    $this->actingAs($sudo)->get(route('permissions.table'))->assertOk();

    Livewire::actingAs($sudo)
        ->test('system::permission.table')
        ->assertSee('Super Admin');
});

it('un usuario con otro role no puede ver el permiso super admin', function () {
    $admin = new_user(role: 'admin');
    $admin->givePermissionTo('permissions.index');
    $this->actingAs($admin)->get(route('permissions.table'))->assertOk();

    Livewire::actingAs($admin)
        ->test('system::permission.table')
        ->assertDontSee('Super Admin');
});

it('puede buscar permisos por nombre, display_name, descripción y key', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo('permissions.index');
    $this->actingAs($user)->get(route('permissions.table'))->assertOk();

    Livewire::actingAs($user)
        ->test('system::permission.table')
        ->set('search', 'users')
        ->assertSee('Crear Usuario');
});

it('puede filtrar permisos por clave', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo('permissions.index');
    $this->actingAs($user)->get(route('permissions.table'))->assertOk();

    Livewire::actingAs($user)
        ->test('system::permission.table')
        ->set('key', 'users')
        ->assertSee('Crear Usuario')
        ->assertDontSee('Crear Rol');
});

it('un usuario sin permiso obtiene un error 403', function () {
    $user = new_user(role: 'user');
    $user->givePermissionTo('permissions.index');
    $firstPermission = Permission::where('name', 'users.create')->first();
    $this->actingAs($user)->get(route('permissions.table'))->assertOk();

    Livewire::actingAs($user)
        ->test('system::permission.table')
        ->call('editPermission', $firstPermission->id)
        ->assertStatus(403);
});

it('al editar un permiso se muestran los datos correctos', function () {
    $user = new_user(role: 'admin');
    $user->givePermissionTo(['permissions.index', 'permissions.edit']);
    $firstPermission = Permission::where('name', 'users.create')->first();
    $this->actingAs($user)->get(route('permissions.table'))->assertOk();

    Livewire::actingAs($user)
        ->test('system::permission.table')
        ->call('editPermission', $firstPermission->id)
        ->assertSet('display_name', $firstPermission->display_name)
        ->assertSet('description', $firstPermission->description)
        ->set('display_name', 'Create New Users')
        ->set('description', 'Allows creating new users with specific details')
        ->call('savePermission');

    $this->assertDatabaseHas('permissions', [
        'id' => $firstPermission->id,
        'display_name' => 'Create New Users',
        'description' => 'Allows creating new users with specific details',
    ]);
});
