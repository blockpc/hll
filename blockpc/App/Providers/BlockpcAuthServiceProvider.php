<?php

declare(strict_types=1);

namespace Blockpc\App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

final class BlockpcAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * Excluir esta habilidad (o un prefijo si quieres varias)
     * if ($ability === 'toma-can-be-activated') {
     *      return null; // deja que corra la policy
     * }
     *
     * Si quieres excluir todas las "toma-..."
     * if (str_starts_with($ability, 'toma-')) {
     *      return null;
     * }
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability, array $arguments = []) {
            return ($user->hasRole('sudo') || $user->hasPermissionTo('super admin'))
                ? true
                : null;
        });
    }
}
