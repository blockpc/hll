<?php

namespace App\Livewire\Notifications;

use App\Models\User;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Table extends Component
{
    use PaginationTrait;

    public User $user;

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->paginate = 6;
    }

    public function render(): View
    {
        return view('livewire.notifications.table', [
            'allCount' => $this->user->notifications()->count(),
            'unreadCount' => $this->user->unreadNotifications()->count(),
            'readCount' => $this->user->readNotifications()->count(),
        ]);
    }

    #[Computed()]
    public function notifications(): LengthAwarePaginator
    {
        return $this->user->notifications()->latest()->paginate($this->paginate);
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = $this->user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        $this->dispatch('update-notifications')->to(OpenClosePanel::class);
    }

    public function markAsUnread(string $notificationId): void
    {
        $notification = $this->user->notifications()->findOrFail($notificationId);
        $notification->markAsUnread();

        $this->dispatch('update-notifications')->to(OpenClosePanel::class);
    }
}
