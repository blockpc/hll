<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Role $role;

    public string $display_name = '';

    public string $description = '';

    public array $permissions_selecteds = [];

    public string $key = '';

    public string $permissions_search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('roles.edit'), 403);

        $this->display_name = $this->role->display_name;
        $this->description = $this->role->description;
        $this->permissions_selecteds = $this->role->permissions()->pluck('name')->values()->all();
    }

    #[Computed()]
    public function permissions(): Collection
    {
        return Permission::query()
            ->visibleToUser()
            ->byKey($this->key)
            ->search($this->permissions_search)
            ->get();
    }

    #[Computed]
    public function keywords(): \Illuminate\Support\Collection
    {
        return Permission::query()
            ->visibleToUser()
            ->distinct()
            ->select('key')
            ->pluck('key', 'key');
    }

    public function save(): mixed
    {
        $this->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'permissions_selecteds' => 'array',
            'permissions_selecteds.*' => 'string|exists:permissions,name',
        ]);

        if ($this->checkSuperAdminPermission($this->permissions_selecteds) === false) {
            return null;
        }

        $sluggedName = Str::slug($this->display_name);
        if ($this->checkSluggedName($sluggedName) === false) {
            return null;
        }

        DB::transaction(function () use ($sluggedName) {
            $this->role->update([
                'name' => $sluggedName,
                'display_name' => $this->display_name,
                'description' => $this->description,
            ]);

            $this->role->syncPermissions($this->permissions_selecteds);
        });

        session()->flash('success', __('system.roles.edit.success_message'));

        return redirect()->route('roles.table');
    }

    private function checkSluggedName(string $sluggedName): bool
    {
        if (Role::where('name', $sluggedName)->where('id', '!=', $this->role->id)->exists()) {
            $this->addError('display_name', __('system.roles.name_already_exists'));

            return false;
        }

        return true;
    }

    private function checkSuperAdminPermission(array $permissions): bool
    {
        if (in_array('super admin', $permissions, true) && ! auth()->user()->hasRole('sudo')) {
            $this->addError('permissions_selecteds', __('system.roles.super_admin_permission_error'));

            return false;
        }

        return true;
    }
};
