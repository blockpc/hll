<?php

declare(strict_types=1);

use App\Mail\NewUserCreatedMail;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses()->group('users', 'system');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// UsersCreateTest

it('un usuario sin permiso no puede acceder a crear un usuario', function () {
    $this->actingAs($this->user)->get(route('users.create'))->assertForbidden();
});

it('un usuario con permiso puede acceder a crear un usuario', function () {
    $this->user->givePermissionTo('users.create');

    $this->actingAs($this->user)->get(route('users.create'))->assertOk();
});

it('un usuario con permiso puede crear un usuario', function () {
    $this->user->givePermissionTo('users.create');

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'test@mail.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('save')
        ->assertRedirect(route('users.table'));

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@mail.com',
    ]);
});

it('no puede crear un usuario con un email duplicado', function () {
    $this->user->givePermissionTo('users.create');
    $existingUser = \App\Models\User::factory()->create(['email' => 'mail@mail.com']);

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('save')
        ->assertHasErrors(['email']);
});

it('no puede crear un usuario si las contraseñas no coinciden', function () {
    $this->user->givePermissionTo('users.create');

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password2')
        ->call('save')
        ->assertHasErrors(['password']);
});

it('se puede crear un usuario sin password', function () {
    $this->user->givePermissionTo('users.create');

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);
    $this->assertNotEmpty($user->password);
});

it('un nuevo usuario puede tener uno o mas roles', function () {
    $this->user->givePermissionTo('users.create');

    $role1 = Role::factory()->create();
    $role2 = Role::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->set('selectedRolesIds', [$role1->id, $role2->id])
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasRole($role1->name));
    $this->assertTrue($user->hasRole($role2->name));
});

it('un nuevo usuario puede tener uno o mas roles via select2', function () {
    $this->user->givePermissionTo('users.create');

    $role1 = Role::factory()->create();
    $role2 = Role::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->call('selectRole', $role1->id)
        ->call('selectRole', $role2->id)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasRole($role1->name));
    $this->assertTrue($user->hasRole($role2->name));
});

it('un nuevo usuario puede tener uno o mas permisos', function () {
    $this->user->givePermissionTo('users.create');

    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->set('selectedPermissionsIds', [$permission1->id, $permission2->id])
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasPermissionTo($permission1->name));
    $this->assertTrue($user->hasPermissionTo($permission2->name));
});

it('un nuevo usuario puede tener uno o mas permisos via select2', function () {
    $this->user->givePermissionTo('users.create');

    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->call('selectPermission', $permission1->id)
        ->call('selectPermission', $permission2->id)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasPermissionTo($permission1->name));
    $this->assertTrue($user->hasPermissionTo($permission2->name));
});

it('verificar que un correo de bienvenida es enviado', function () {
    Mail::fake();
    $this->user->givePermissionTo('users.create');

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->call('save')
        ->assertHasNoErrors();

    Mail::assertSent(NewUserCreatedMail::class, function ($mail) {
        return $mail->hasTo('mail@mail.com');
    });
});

it('no envía correo de bienvenida cuando send_email es false', function () {
    Mail::fake();
    $this->user->givePermissionTo('users.create');

    Livewire::actingAs($this->user)
        ->test('system::users.create')
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('auto_password', true)
        ->set('send_email', false)
        ->call('save')
        ->assertHasNoErrors();

    Mail::assertNotSent(NewUserCreatedMail::class);
});
