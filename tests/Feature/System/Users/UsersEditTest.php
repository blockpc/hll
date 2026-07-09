<?php

use App\Mail\ResendUserEmailVerificationMail;
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

// UsersEditTest

it('un usuario sin permiso no puede acceder a editar un usuario', function () {
    $user = User::factory()->create();
    $this->actingAs($this->user)->get(route('users.edit', ['user' => $user]))->assertForbidden();
});

it('un usuario con permiso puede acceder a editar un usuario', function () {
    $this->user->givePermissionTo('users.edit');
    $user = User::factory()->create();

    $this->actingAs($this->user)->get(route('users.edit', ['user' => $user]))->assertOk();
});

it('un usuario con permiso puede editar un usuario', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
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

it('no puede editar un usuario con un email duplicado', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');
    $existingUser = User::factory()->create(['email' => 'mail@mail.com']);

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('save')
        ->assertHasErrors(['email']);
});

it('no puede editar un usuario si las contraseñas no coinciden', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password2')
        ->call('save')
        ->assertHasErrors(['password']);
});

it('un usuario puede tener uno o mas roles', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    $role1 = Role::factory()->create();
    $role2 = Role::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('selectedRolesIds', [$role1->id, $role2->id])
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasRole($role1->name));
    $this->assertTrue($user->hasRole($role2->name));
});

it('un usuario puede tener uno o mas roles via select2', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    $role1 = Role::factory()->create();
    $role2 = Role::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->call('selectRole', $role1->id)
        ->call('selectRole', $role2->id)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasRole($role1->name));
    $this->assertTrue($user->hasRole($role2->name));
});

it('al seleccionar un rol via select2 se refleja inmediatamente en el usuario', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    $role = Role::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->call('selectRole', $role->id)
        ->assertHasNoErrors();

    expect($user->fresh()->hasRole($role->name))->toBeTrue();
});

it('un usuario puede tener uno o mas permisos', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->set('selectedPermissionsIds', [$permission1->id, $permission2->id])
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasPermissionTo($permission1->name));
    $this->assertTrue($user->hasPermissionTo($permission2->name));
});

it('un nuevo usuario puede tener uno o mas permisos via select2', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->set('name', 'Test User')
        ->set('email', 'mail@mail.com')
        ->call('selectPermission', $permission1->id)
        ->call('selectPermission', $permission2->id)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'mail@mail.com')->first();
    $this->assertNotNull($user);

    $this->assertTrue($user->hasPermissionTo($permission1->name));
    $this->assertTrue($user->hasPermissionTo($permission2->name));
});

it('al seleccionar un permiso via select2 se refleja inmediatamente en el usuario', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    $permission = Permission::factory()->create();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->call('selectPermission', $permission->id)
        ->assertHasNoErrors();

    expect($user->fresh()->hasPermissionTo($permission->name))->toBeTrue();
});

it('puedo marcar un usuario no verificado como verificado', function () {
    $user = User::factory()->unverified()->create();
    $this->user->givePermissionTo('users.edit');

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->call('markEmailAsVerified')
        ->assertSet('user_has_verified_email', true)
        ->assertHasNoErrors();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('puedo marcar un usuario como no verificado', function () {
    $user = User::factory()->create();
    $this->user->givePermissionTo('users.edit');

    expect($user->hasVerifiedEmail())->toBeTrue();

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->call('markEmailAsUnverified')
        ->assertSet('user_has_verified_email', false)
        ->assertHasNoErrors();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('puedo enviar un correo de verificacion de email', function () {
    Mail::fake();
    $user = User::factory()->unverified()->create();
    $this->user->givePermissionTo('users.edit');

    Livewire::actingAs($this->user)
        ->test('system::users.edit', ['user' => $user])
        ->call('resendVerificationEmail')
        ->assertHasNoErrors();

    Mail::assertSent(ResendUserEmailVerificationMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
