<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('roles', 'system');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

it('un usuario sin permiso no puede acceder a crear un rol', function () {
    $this->actingAs($this->user)->get(route('roles.create'))->assertForbidden();
});

it('un usuario con permiso puede acceder a crear un rol', function () {
    $this->user->givePermissionTo('roles.create');

    $this->actingAs($this->user)->get(route('roles.create'))->assertOk();
});

it('un usuario con permiso puede crear un rol', function () {
    $this->user->givePermissionTo('roles.create');

    Livewire::actingAs($this->user)
        ->test('system::roles.create')
        ->set('display_name', 'Test Role')
        ->set('description', 'This is a test role.')
        ->set('permissions_selecteds', ['users.index', 'users.create'])
        ->call('save')
        ->assertRedirect(route('roles.table'));

    $this->assertDatabaseHas('roles', [
        'display_name' => 'Test Role',
        'description' => 'This is a test role.',
    ]);

    $role = Role::where('display_name', 'Test Role')->first();
    $this->assertTrue($role->hasPermissionTo('users.index'));
    $this->assertTrue($role->hasPermissionTo('users.create'));
});

it('un usuario sin rol sudo no puede crear un rol y asignar el permiso super admin', function () {
    $this->user->givePermissionTo('roles.create');

    Livewire::actingAs($this->user)
        ->test('system::roles.create')
        ->set('display_name', 'Test Role')
        ->set('description', 'This is a test role.')
        ->set('permissions_selecteds', ['super admin', 'users.index'])
        ->call('save')
        ->assertHasErrors(['permissions_selecteds']);
});

it('un usuario con rol sudo puede crear un rol y asignar el permiso super admin', function () {
    $this->user->assignRole('sudo');

    Livewire::actingAs($this->user)
        ->test('system::roles.create')
        ->set('display_name', 'Test Role')
        ->set('description', 'This is a test role.')
        ->set('permissions_selecteds', ['super admin'])
        ->call('save')
        ->assertRedirect(route('roles.table'));

    $this->assertDatabaseHas('roles', [
        'display_name' => 'Test Role',
        'description' => 'This is a test role.',
    ]);

    $role = Role::where('display_name', 'Test Role')->first();
    $this->assertTrue($role->hasPermissionTo('super admin'));
});

it('mantiene la seleccion de permisos al cambiar la clave de filtrado', function () {
    $this->user->givePermissionTo('roles.create');

    $selectedPermission = Permission::query()
        ->where('name', '!=', 'super admin')
        ->firstOrFail();

    $filteredPermission = Permission::query()
        ->where('name', '!=', 'super admin')
        ->where('key', '!=', $selectedPermission->key)
        ->firstOrFail();

    Livewire::actingAs($this->user)
        ->test('system::roles.create')
        ->set('permissions_selecteds', [$selectedPermission->name])
        ->set('key', $filteredPermission->key)
        ->assertSet('permissions_selecteds', [$selectedPermission->name])
        ->assertSee($filteredPermission->display_name)
        ->assertDontSee($selectedPermission->display_name);
});
