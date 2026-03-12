<?php

declare(strict_types=1);

namespace App\Livewire\Notifications\Traits;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

trait Select2UsersNotificationsTrait
{
    public string $searchUser = '';

    public int $selectedUserId = 0;

    public string $selectedUserName = '';

    #[Computed()]
    public function users(): Collection
    {
        return User::query()
            ->notCurrentUser()
            ->search($this->searchUser)
            ->pluck('name', 'id');
    }

    public function selectUser(?int $userId = null): void
    {
        if (is_null($userId)) {
            $this->reset('searchUser', 'selectedUserId', 'selectedUserName');

            return;
        }
        $this->searchUser = '';
        $this->selectedUserId = $userId;
        $this->selectedUserName = $this->users()->get($userId, '');
    }
}
