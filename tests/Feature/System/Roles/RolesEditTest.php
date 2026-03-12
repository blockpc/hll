<?php

use App\Models\Role;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

uses()->group('roles', 'system');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

it('un usuario sin permiso no puede acceder a editar un rol', function () {
    $role = Role::factory()->create();

    $this->actingAs($this->user)->get(route('roles.edit', $role))->assertForbidden();
});

it('un usuario con permiso puede acceder a editar un rol', function () {
    $this->user->givePermissionTo('roles.edit');
    $role = Role::factory()->create();
    $this->actingAs($this->user)->get(route('roles.edit', $role))->assertOk();
});

it('un usuario con permiso puede editar un rol', function () {
    $this->user->givePermissionTo('roles.edit');
    $role = Role::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::roles.edit', ['role' => $role])
        ->set('display_name', 'Updated Role Name')
        ->set('description', 'Updated description for the role.')
        ->call('save')
        ->assertRedirect(route('roles.table'));

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'display_name' => 'Updated Role Name',
        'description' => 'Updated description for the role.',
    ]);
});

it('un usuario sin rol sudo no puede actualizar un rol y asignar el permiso super admin', function () {
    $role = Role::factory()->create();
    $this->user->givePermissionTo('roles.edit');

    Livewire::actingAs($this->user)
        ->test('system::roles.edit', ['role' => $role])
        ->set('display_name', 'Test Role')
        ->set('description', 'This is a test role.')
        ->set('permissions_selecteds', ['super admin', 'users.index'])
        ->call('save')
        ->assertHasErrors(['permissions_selecteds']);
});

it('un usuario con rol sudo puede actualizar un rol y asignar el permiso super admin', function () {
    $role = Role::factory()->create();
    $this->user->assignRole('sudo');

    Livewire::actingAs($this->user)
        ->test('system::roles.edit', ['role' => $role])
        ->set('display_name', 'Test Role')
        ->set('description', 'This is a test role.')
        ->set('permissions_selecteds', ['super admin'])
        ->call('save')
        ->assertRedirect(route('roles.table'));

    $this->assertDatabaseHas('roles', [
        'display_name' => 'Test Role',
        'description' => 'This is a test role.',
    ]);

    $this->assertTrue($role->fresh()->hasPermissionTo('super admin'));
});

it('al abrir edicion se precargan los permisos actuales del rol', function () {
    $this->user->givePermissionTo('roles.edit');

    $role = Role::factory()->create();
    $role->givePermissionTo(['users.index', 'users.create']);

    $selectedPermissions = Livewire::actingAs($this->user)
        ->test('system::roles.edit', ['role' => $role])
        ->get('permissions_selecteds');

    expect($selectedPermissions)->toBeArray();
    expect(array_values($selectedPermissions))->toEqualCanonicalizing(['users.index', 'users.create']);
});
