<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\Select2PermissionsTrait;
use App\Traits\Select2RolesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Editar usuario')] class extends Component
{
    use Select2PermissionsTrait;
    use Select2RolesTrait;

    public User $user;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403, __('system.users.403.users-edit'));

        $this->loadRolesIds();
        $this->loadPermissionsIds();

        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function save(): mixed
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            if ($this->password) {
                $this->user->update(['password' => Hash::make($this->password)]);
            }

            $roles = Role::query()
                ->visibleToUser()
                ->whereIn('id', $this->selectedRolesIds)
                ->get();

            $permissions = Permission::query()
                ->visibleToUser()
                ->whereIn('id', $this->selectedPermissionsIds)
                ->get();

            $this->user->syncRoles($roles);
            $this->user->syncPermissions($permissions);
        });

        session()->flash('success', __('system.users.edit.success_message', ['name' => $this->user->name]));

        return redirect()->route('users.table');
    }
};
