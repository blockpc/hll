<?php

use App\Models\Role;
use Blockpc\App\Rules\AreEqualsRule;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use PaginationTrait;

    public bool $deleteModalVisible = false;

    public ?int $roleToDeleteId = null;

    public string $current_name = '';

    public string $name = '';

    public string $password = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('roles.index'), 403, __('system.roles.403.roles-index'));
    }

    #[Computed()]
    public function roles(): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->visibleToUser()
            ->search($this->search)
            ->paginate($this->paginate);
    }

    public function confirmDelete(int $roleId): void
    {
        abort_unless(auth()->user()?->can('roles.delete'), 403, __('system.roles.403.roles-delete'));

        $this->roleToDeleteId = $roleId;
        $roleToDelete = Role::query()->find($this->roleToDeleteId);

        if (! $roleToDelete) {
            $this->cancel();
            session()->flash('error', __('system.roles.delete.no_role_selected'));

            return;
        }

        $this->current_name = $roleToDelete->display_name;

        $this->deleteModalVisible = true;
    }

    public function cancel(): void
    {
        $this->reset(['deleteModalVisible', 'roleToDeleteId', 'current_name', 'name', 'password']);
    }

    public function destroyRole(): void
    {
        $this->validate([
            'name' => ['required', new AreEqualsRule($this->current_name, __('system.roles.delete.invalid_role_name'))],
            'password' => 'required|current_password',
        ]);

        if (! $this->roleToDeleteId) {
            $this->cancel();
            session()->flash('error', __('system.roles.delete.no_role_selected'));

            return;
        }

        $roleToDelete = Role::query()->find($this->roleToDeleteId);

        if (! $roleToDelete) {
            $this->cancel();
            session()->flash('error', __('system.roles.delete.no_role_selected'));

            return;
        }

        if ($roleToDelete->name === 'sudo') {
            session()->flash('error', __('system.roles.delete.cannot_delete_role'));
            $this->cancel();

            return;
        }

        $roleToDelete->delete();
        $this->cancel();
        session()->flash('success', __('system.roles.delete.success_message'));
    }
};
