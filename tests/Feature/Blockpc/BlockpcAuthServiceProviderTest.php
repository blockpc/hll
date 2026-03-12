<?php

use Blockpc\App\Providers\BlockpcAuthServiceProvider;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Gate;

uses()->group('blockpc');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    app()->register(BlockpcAuthServiceProvider::class);

    Gate::define('blockpc-test-ability', function () {
        return false;
    });
});

it('otorga acceso total a usuario con rol sudo', function () {
    $user = new_user(role: 'sudo');

    expect(Gate::forUser($user)->allows('blockpc-test-ability'))->toBeTrue();
});

it('otorga acceso total a usuario con permiso super admin', function () {
    $user = new_user(role: 'user');
    $user->givePermissionTo('super admin');

    expect(Gate::forUser($user)->allows('blockpc-test-ability'))->toBeTrue();
});

it('no hace bypass para un usuario regular', function () {
    $user = new_user(role: 'user');

    expect(Gate::forUser($user)->allows('blockpc-test-ability'))->toBeFalse();
});
