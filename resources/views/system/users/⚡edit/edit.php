<?php

use App\Mail\ResendUserEmailVerificationMail;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\Select2PermissionsTrait;
use App\Traits\Select2RolesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public bool $user_has_verified_email = false;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403, __('system.users.403.users-edit'));

        $this->loadRolesIds();
        $this->loadPermissionsIds();

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->user_has_verified_email = $this->user->hasVerifiedEmail();
    }

    public function save(): mixed
    {
        abort_unless(auth()->user()?->can('users.edit'), 403, __('system.users.403.users-edit'));

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

    public function markEmailAsVerified(): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403, __('system.users.403.users-edit'));

        if (! $this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();
            $this->user_has_verified_email = true;

            session()->flash('success-email-change-verified', __('system.users.edit.user_email_verification.email_verified_success_message'));
        }
    }

    public function markEmailAsUnverified(): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403, __('system.users.403.users-edit'));

        if ($this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsUnverified();
            $this->user_has_verified_email = false;

            session()->flash('success-email-change-unverified', __('system.users.edit.user_email_verification.email_unverified_success_message'));
        }
    }

    public function resendVerificationEmail(): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403, __('system.users.403.users-edit'));

        if (! $this->user->hasVerifiedEmail()) {

            try {
                Mail::to($this->user->email)->send(new ResendUserEmailVerificationMail($this->user));

                session()->flash('success-email-change-resend', __('system.users.edit.user_email_verification.success_resend_verification_email'));
            } catch (\Exception $e) {
                logger()->error('Failed to resend user email verification', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'error' => $e->getMessage(),
                ]);
                session()->flash('error-email-change-resend', __('system.users.edit.user_email_verification.error_email_change_resend'));
            }
        }
    }
};
