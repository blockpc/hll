<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $display_name = '';

    public string $description = '';

    public array $permissions_selecteds = [];

    public string $key = '';

    public string $permissions_search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('roles.create'), 403, __('system.roles.403.roles-create'));
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

    public function save(): RedirectResponse|Redirector|null
    {
        abort_unless(auth()->user()?->can('roles.create'), 403, __('system.roles.403.roles-create'));

        $this->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'permissions_selecteds' => 'array',
            'permissions_selecteds.*' => 'string|exists:permissions,name',
        ]);

        if (in_array('super admin', $this->permissions_selecteds) && ! auth()->user()->hasRole('sudo')) {
            $this->addError('permissions_selecteds', __('system.roles.super_admin_permission_error'));

            return null;
        }

        $sluggedName = Str::slug($this->display_name);
        if (Role::where('name', $sluggedName)->exists()) {
            $this->addError('display_name', __('system.roles.name_already_exists'));

            return null;
        }

        DB::transaction(function () use ($sluggedName) {
            $role = Role::create([
                'name' => $sluggedName,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'is_editable' => true,
            ]);

            $role->syncPermissions($this->permissions_selecteds);
        });

        session()->flash('success', __('system.roles.create.success_message'));

        return redirect()->route('roles.table');
    }
};
