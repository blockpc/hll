<?php

declare(strict_types=1);

namespace Blockpc\Traits;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

trait AuthorizesRoleOrPermissionTrait
{
    /**
     * Authorize the user for a given role or permission.
     * If user is not logged in, throw an exception.
     * If user has the super admin role (configurable via 'permission.super_admin_role'), return true.
     * If user does not have the role or permission, return false.
     * If user has the role or permission, return true.
     */
    public function authorizeRoleOrPermission(string|array $roleOrPermission, ?string $guard = null): bool
    {
        if (Auth::guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        $rolesOrPermissions = array_filter($rolesOrPermissions);

        if (empty($rolesOrPermissions)) {
            return false;
        }

        $auth = Auth::guard($guard)->user();

        if ($auth->hasRole(config('permission.super_admin_role', 'sudo'))) {
            return true;
        }

        if ($auth->hasAnyRole($rolesOrPermissions) || $auth->hasAnyPermission($rolesOrPermissions)) {
            return true;
        }

        return false;
    }
}
