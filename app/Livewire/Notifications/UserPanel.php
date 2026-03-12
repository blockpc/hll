<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use App\Livewire\Notifications\Traits\Select2UsersNotificationsTrait;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Blockpc\Traits\AlertBrowserEvent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class UserPanel extends Component
{
    use AlertBrowserEvent, Select2UsersNotificationsTrait;

    public bool $open = false;

    public string $title = '';

    public string $content = '';

    public function getListeners(): array
    {
        return [
            'echo-private:App.Models.User.'.auth()->id().',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'refreshFromBroadcast',
        ];
    }

    public function render(): View
    {
        $notifications = auth()->user()->unreadNotifications;

        return view('livewire.notifications.user-panel', [
            'notifications' => $notifications,
        ]);
    }

    public function send(): void
    {
        $this->validate([
            'title' => 'required|string|max:64',
            'content' => 'required|string|max:255',
            'selectedUserId' => 'required|exists:users,id',
        ], [
            'selectedUserId.required' => 'Debe seleccionar un usuario.',
            'selectedUserId.exists' => 'El usuario seleccionado no existe.',
        ], [
            'title' => 'Titulo',
            'content' => 'Contenido',
            'selectedUserId' => 'Usuario destinatario',
        ]);

        $recipient = User::findOrFail($this->selectedUserId);

        $authUser = auth()->user();

        $recipient->notify(new NewMessageNotification(
            title: $this->title,
            content: $this->content,
            sentAt: now()->format('d/m/Y H:i'),
            fromName: $authUser->name,
            fromId: $authUser->id,
        ));

        $this->reset('title', 'content', 'selectedUserId', 'selectedUserName');

        $this->alert(
            message: 'Notificación enviada correctamente.',
            type: 'success',
            title: 'Nueva Notificación',
        );
    }

    #[On('open-notifications')]
    public function openPanel(): void
    {
        $this->open = true;
    }

    #[On('close-notifications')]
    public function closePanel(): void
    {
        $this->open = false;
    }

    public function markAsRead(string $id): void
    {
        $notification = auth()->user()->unreadNotifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            $this->alert(
                message: 'Notificación marcada como leída.',
                type: 'success',
                title: 'Notificación Leída',
            );
        }

        $this->dispatch('update-notifications')->to('notifications.open-close-panel');
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        $this->alert(
            message: 'Todas las notificaciones fueron marcadas como leídas.',
            type: 'success',
            title: 'Notificaciones Leídas',
        );

        $this->dispatch('update-notifications')->to('notifications.open-close-panel');
    }

    public function getUnreadCountProperty(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function updatedOpen(bool $value): void
    {
        if (! $value) {
            $this->reset('searchUser', 'selectedUserId', 'selectedUserName');
        }
    }

    public function refreshFromBroadcast(): void
    {
        $this->dispatch('update-notifications')->to('notifications.open-close-panel');
    }
}
