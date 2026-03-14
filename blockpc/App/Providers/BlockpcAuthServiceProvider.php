<?php

declare(strict_types=1);

namespace Blockpc\App\Providers;

use App\Models\User;
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
        Gate::before(function (?User $user, string $ability, array $arguments = []) {
            if (! $user) {
                return null;
            }

            $superAdminRole = (string) config('permission.super_admin_role', 'sudo');
            if ($user->hasRole($superAdminRole)) {
                return true;
            }

            return $user->checkPermissionTo('super admin') ? true : null;
        });
    }
}
