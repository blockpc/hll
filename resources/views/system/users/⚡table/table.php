<?php

use App\Models\User;
use Blockpc\App\Rules\AreEqualsRule;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Lista de Usuarios')] class extends Component
{
    use PaginationTrait;

    public bool $deleteModalVisible = false;

    public ?int $userToDeleteId = null;

    public string $current_name = '';

    public string $name = '';

    public string $password = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.index'), 403, __('system.users.403.users-index'));
    }

    #[Computed()]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->withCount(['roles', 'permissions'])
            ->visibleToUser()
            ->search($this->search)
            ->paginate($this->paginate);
    }

    public function confirmDelete(int $userId): void
    {
        abort_unless(auth()->user()?->can('users.delete'), 403, __('system.users.403.users-delete'));

        $this->userToDeleteId = $userId;
        $userToDelete = User::query()->find($this->userToDeleteId);

        if (! $userToDelete) {
            $this->cancel();
            session()->flash('error', __('system.users.delete.no_user_selected'));

            return;
        }

        $this->current_name = $userToDelete->name;

        $this->deleteModalVisible = true;
    }

    public function cancel(): void
    {
        $this->reset(['deleteModalVisible', 'userToDeleteId', 'current_name', 'name', 'password']);
    }

    public function destroyUser(): void
    {
        $this->validate([
            'name' => ['required', new AreEqualsRule($this->current_name, __('system.users.delete.invalid_user_name'))],
            'password' => 'required|current_password',
        ]);

        $userToDelete = User::query()->find($this->userToDeleteId);

        if (! $userToDelete) {
            $this->cancel();
            session()->flash('error', __('system.users.delete.no_user_selected'));

            return;
        }

        if ($userToDelete->id === auth()->id()) {
            $this->cancel();
            session()->flash('error', __('system.users.delete.cannot_delete_yourself'));

            return;
        }

        if ($userToDelete->hasRole('sudo')) {
            $this->cancel();
            session()->flash('error', __('system.users.delete.cannot_delete_sudo'));

            return;
        }

        $userToDelete->delete();

        $this->cancel();
        session()->flash('success', __('system.users.delete.success_message'));
    }
};
