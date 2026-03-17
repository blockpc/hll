<?php

use App\Livewire\Notes\ListNotes;
use App\Livewire\Notifications\Table;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/email/verify/invitation/{id}/{hash}', function (Request $request, int $id, string $hash) {
    $user = User::query()->findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, __('system.users.403.invalid-verification-link'));
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect()->route('login')->with('status', __('verified'));
})->middleware(['signed', 'throttle:6,1'])->name('verification.invitation.verify');

require __DIR__.'/settings.php';

Route::prefix('sistema')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::get('lista-de-notas', ListNotes::class)->name('notes.index');
        Route::get('mis-notificaciones', Table::class)->name('notifications.table');

        Route::prefix('permisos')->group(function () {
            Route::livewire('/lista-de-permisos', 'system::permission.table')->name('permissions.table');
        });

        Route::prefix('roles')->group(function () {
            Route::livewire('/lista-de-roles', 'system::roles.table')->name('roles.table');
            Route::livewire('/nuevo-rol', 'system::roles.create')->name('roles.create');
            Route::livewire('/editar-rol/{role}', 'system::roles.edit')->name('roles.edit');
        });

        Route::prefix('usuarios')->group(function () {
            Route::livewire('/lista-de-usuarios', 'system::users.table')->name('users.table');
            Route::livewire('/nuevo-usuario', 'system::users.create')->name('users.create');
            Route::livewire('/editar-usuario/{user}', 'system::users.edit')->name('users.edit');
        });

        Route::prefix('clanes')->group(function () {
            Route::livewire('/', 'system::clans.clan-table')->name('clans.table');
            Route::livewire('/nuevo-clan', 'system::clans.clan-create')->name('clans.create');
            Route::livewire('/editar-clan/{clan}', 'system::clans.clan-edit')->name('clans.edit');
            Route::livewire('/ver-clan/{clan}', 'system::clans.clan-show')->name('clans.show');

            Route::livewire('/ver-clan/{clan}/gestionar-ayudantes', 'system::clans.helpers-manager')->name('clans.helpers.manager');
            Route::livewire('/ver-clan/{clan}/gestionar-soldados', 'system::clans.soldiers-manager')->name('clans.soldiers.manager');
        });

        Route::prefix('rosters')->group(function () {
            Route::livewire('/{clan?}', 'system::rosters.roster-table')->name('rosters.table');
            Route::livewire('/{clan}/crear', 'system::rosters.roster-create')->name('rosters.create');
        });
    });
