<?php

declare(strict_types=1);

use App\Mail\NewUserCreatedMail;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\Select2PermissionsTrait;
use App\Traits\Select2RolesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Crear nuevo usuario')] class extends Component
{
    use Select2PermissionsTrait;
    use Select2RolesTrait;

    public string $name = '';

    public string $email = '';

    public bool $auto_password = false;

    public bool $send_email = true;

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.create'), 403, __('system.users.403.users-create'));
    }

    public function save(): mixed
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'auto_password' => 'boolean',
            'password' => $this->auto_password
                ? 'nullable'
                : 'required|string|min:8|confirmed',
        ]);

        if ($this->auto_password) {
            $this->password = Str::random(12);
        }

        try {
            $createdUser = DB::transaction(function (): User {
                $user = User::query()->create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);

                if (! empty($this->selectedRolesIds)) {
                    $roles = Role::query()
                        ->visibleToUser()
                        ->whereIn('id', $this->selectedRolesIds)
                        ->get();
                    $user->assignRole($roles);
                }

                if (! empty($this->selectedPermissionsIds)) {
                    $permissions = Permission::query()
                        ->visibleToUser()
                        ->whereIn('id', $this->selectedPermissionsIds)
                        ->get();
                    $user->givePermissionTo($permissions);
                }

                return $user;
            });
        } catch (\Throwable $exception) {
            logger()->error('Failed to create user', [
                'error' => $exception->getMessage(),
            ]);

            session()->flash('error', __('system.users.create.creation_error_message'));

            return redirect()->route('users.table');
        }

        session()->flash('success', __('system.users.create.success_message', ['name' => $createdUser->name]));
        try {
            if ($this->send_email) {
                Mail::to($this->email)->send(new NewUserCreatedMail($createdUser));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send new user created email', [
                'user_id' => $createdUser->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', __('system.users.create.email_error_message'));
        }

        return redirect()->route('users.table');
    }
};
