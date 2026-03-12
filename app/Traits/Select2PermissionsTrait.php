<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

trait Select2PermissionsTrait
{
    public array $selectedPermissionsIds = [];

    public array $selectedPermissionsNames = [];

    public string $searchPermission = '';

    #[Computed()]
    public function permissions(): Collection
    {
        return Permission::query()
            ->visibleToUser()
            ->search($this->searchPermission)
            ->pluck('display_name', 'id');
    }

    public function selectPermission(int|string $permissionId): void
    {
        $permissionId = (int) $permissionId;
        $isSelected = in_array($permissionId, $this->selectedPermissionsIds, true);

        if ($isSelected) {
            $this->selectedPermissionsIds = array_values(array_diff($this->selectedPermissionsIds, [$permissionId]));
        }

        if (! $isSelected) {
            $this->selectedPermissionsIds[] = $permissionId;
        }

        if (! isset($this->user)) {
            $this->selectedPermissionsNames = Permission::query()
                ->whereIn('id', $this->selectedPermissionsIds)
                ->pluck('display_name', 'id')
                ->toArray();

            return;
        }

        if ($isSelected) {
            $this->user->permissions()->detach($permissionId);
        } else {
            $this->user->permissions()->syncWithoutDetaching([$permissionId]);
        }

        $this->user->load('permissions');
    }

    public function loadPermissionsIds(): void
    {
        $this->selectedPermissionsIds = $this->user->permissions()->pluck('id')->toArray();
    }

    public function deletePermissionId(int|string $permissionId): void
    {
        $permissionId = (int) $permissionId;
        $this->selectedPermissionsIds = array_values(array_diff($this->selectedPermissionsIds, [$permissionId]));

        if (! isset($this->user)) {
            unset($this->selectedPermissionsNames[$permissionId]);

            return;
        }

        $this->user->permissions()->detach($permissionId);
        $this->user->load('permissions');
    }
}
