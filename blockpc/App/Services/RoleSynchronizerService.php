<?php

declare(strict_types=1);

namespace Blockpc\App\Services;

use App\Models\Role;
use Blockpc\App\Lists\PermissionList;
use Blockpc\App\Lists\RoleList;
use Illuminate\Support\Collection;

final class RoleSynchronizerService
{
    private ?Collection $existingRoles = null;

    /**
     * Synchronize only missing roles from RoleList.
     *
     * Existing roles are intentionally not updated to preserve
     * user-managed changes made via UI or API.
     */
    public function sync(): void
    {
        $missing = $this->getMissing();
        foreach ($missing as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'display_name' => $roleData['display_name'] ?? null,
                'description' => $roleData['description'] ?? null,
                'is_editable' => $roleData['is_editable'] ?? true,
                'guard_name' => $this->resolveGuardName($roleData),
            ]);

            $this->assignPermissions($role, $roleData['permissions'] ?? []);
        }

        $this->existingRoles = null;
    }

    private function ensureExistingRolesLoaded(): Collection
    {
        if ($this->existingRoles === null) {
            $this->existingRoles = Role::all()->keyBy(fn ($role) => "{$role->name}|{$role->guard_name}");
        }

        return $this->existingRoles;
    }

    public function getMissing(): Collection
    {
        $existing = $this->ensureExistingRolesLoaded();

        return collect(RoleList::all())
            ->filter(function ($role) use ($existing) {
                $name = $role['name'];
                $guard = $this->resolveGuardName($role);

                return ! $existing->has("{$name}|{$guard}");
            });
    }

    public function getOrphans(): Collection
    {
        $existing = $this->ensureExistingRolesLoaded();
        $defined = collect(RoleList::all());

        return $existing->filter(function ($role) use ($defined) {
            return ! $defined->contains(function ($definedRole) use ($role) {
                $name = $definedRole['name'];
                $guard = $this->resolveGuardName($definedRole);

                return $name === $role->name && $guard === $role->guard_name;
            });
        });
    }

    public function getOutdated(): Collection
    {
        $existing = $this->ensureExistingRolesLoaded();

        return collect(RoleList::all())
            ->filter(function ($role) use ($existing) {
                $name = $role['name'];
                $guard = $this->resolveGuardName($role);
                $role = $existing->get("{$name}|{$guard}");

                if (! $role) {
                    return false;
                }

                return $role->name !== $name;
            });
    }

    public function prune(): int
    {
        $orphans = $this->getOrphans();
        $deleted = 0;
        foreach ($orphans as $orphan) {
            if ($orphan->is_editable) {
                $orphan->delete();
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * @param array{
     *   name: string,
     *   display_name?: string,
     *   description?: string,
     *   is_editable?: bool,
     *   guard_name?: string,
     *   guard?: string,
     *   permissions: array<int, string>
     * } $roleData
     */
    private function resolveGuardName(array $roleData): string
    {
        return $roleData['guard_name'] ?? $roleData['guard'] ?? 'web';
    }

    /**
     * Assign permissions only when creating missing roles.
     *
     * Existing roles are not synchronized by design.
     *
     * @param  array<int, string>  $permissions
     */
    private function assignPermissions(Role $role, array $permissions): void
    {
        $hasWildcard = in_array('*', $permissions, true);

        if (! $hasWildcard && ! empty($permissions)) {
            $role->syncPermissions($permissions);

            return;
        }

        if ($hasWildcard) {
            $allAvailablePermissions = collect(PermissionList::all())
                ->filter(fn ($perm) => $perm['key'] !== 'sudo')
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($allAvailablePermissions);
        }
    }
}
