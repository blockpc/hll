<?php

use App\Models\Permission;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use PaginationTrait;

    public string $key = '';

    public bool $showEditModal = false;

    public int $editingPermissionId = 0;

    public string $display_name = '';

    public string $description = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('permissions.index'), 403, __('system.permissions.403.permissions-index'));
    }

    #[Computed()]
    public function permissions(): LengthAwarePaginator
    {
        return Permission::with('roles')
            ->visibleToUser()
            ->byKey($this->key)
            ->search($this->search)
            ->paginate($this->paginate);
    }

    #[Computed]
    public function keywords(): Collection
    {
        return Permission::query()
            ->visibleToUser()
            ->distinct()
            ->select('key')
            ->pluck('key', 'key');
    }

    public function updatedKey(): void
    {
        $this->resetPage();
    }

    public function editPermission(int $permissionId): void
    {
        abort_unless(auth()->user()?->can('permissions.edit'), 403, __('system.permissions.403.permissions-edit'));

        $permission = Permission::findOrFail($permissionId);
        $this->editingPermissionId = $permissionId;
        $this->display_name = $permission->display_name;
        $this->description = $permission->description;
        $this->showEditModal = true;
    }

    public function savePermission(): void
    {
        abort_unless(auth()->user()?->can('permissions.edit'), 403, __('system.permissions.403.permissions-edit'));

        $this->validate(
            [
                'display_name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ],
            [
                'display_name.required' => __('system.permissions.edit.validations.display_name.required'),
                'display_name.string' => __('system.permissions.edit.validations.display_name.string'),
                'display_name.max' => __('system.permissions.edit.validations.display_name.max'),
                'description.required' => __('system.permissions.edit.validations.description.required'),
                'description.string' => __('system.permissions.edit.validations.description.string'),
                'description.max' => __('system.permissions.edit.validations.description.max'),
            ], [
                'display_name' => __('system.permissions.edit.display_name'),
                'description' => __('system.permissions.edit.description'),
            ]
        );

        $permission = Permission::findOrFail($this->editingPermissionId);
        $permission->display_name = $this->display_name;
        $permission->description = $this->description;
        $permission->save();

        $this->reset(['editingPermissionId', 'display_name', 'description']);
        $this->showEditModal = false;
    }

    public function cancel(): void
    {
        $this->reset(['editingPermissionId', 'display_name', 'description']);
        $this->showEditModal = false;
    }
};
