<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class OpenClosePanel extends Component
{
    public function render(): View
    {
        return view('livewire.notifications.open-close-panel', [
            'unreadCount' => auth()->user()?->unreadNotifications()->count() ?? 0,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $userId = auth()->id();

        if ($userId === null) {
            return [];
        }

        return [
            'update-notifications' => 'refresh',
            'echo-private:App.Models.User.'.$userId.',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'refresh',
        ];
    }

    /**
     * Triggers a re-render to recalculate unreadCount.
     */
    public function refresh(): void {}
}
