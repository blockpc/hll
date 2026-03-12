<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Role;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

trait Select2RolesTrait
{
    public array $selectedRolesIds = [];

    public array $selectedRolesNames = [];

    public string $searchRole = '';

    #[Computed()]
    public function roles(): Collection
    {
        return Role::query()
            ->visibleToUser()
            ->search($this->searchRole)
            ->pluck('display_name', 'id');
    }

    public function selectRole(int|string $roleId): void
    {
        $roleId = (int) $roleId;
        $isSelected = in_array($roleId, $this->selectedRolesIds, true);

        if ($isSelected) {
            $this->selectedRolesIds = array_values(array_diff($this->selectedRolesIds, [$roleId]));
        }

        if (! $isSelected) {
            $this->selectedRolesIds[] = $roleId;
        }

        if (! isset($this->user)) {
            $this->selectedRolesNames = Role::query()
                ->whereIn('id', $this->selectedRolesIds)
                ->pluck('display_name', 'id')
                ->toArray();

            return;
        }

        if ($isSelected) {
            $this->user->roles()->detach($roleId);
            $this->user->load('roles');

            return;
        }

        $this->user->roles()->syncWithoutDetaching([$roleId]);
        $this->user->load('roles');
    }

    public function loadRolesIds(): void
    {
        $this->selectedRolesIds = $this->user->roles()->pluck('id')->toArray();
    }

    public function deleteRoleId(int|string $roleId): void
    {
        $roleId = (int) $roleId;
        $this->selectedRolesIds = array_values(array_diff($this->selectedRolesIds, [$roleId]));

        if (! isset($this->user)) {
            unset($this->selectedRolesNames[$roleId]);

            return;
        }

        $this->user->roles()->detach($roleId);
        $this->user->load('roles');
    }
}
