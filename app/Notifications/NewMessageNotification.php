<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $content,
        public string $sentAt,
        public string $fromName,
        public int $fromId = 0,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->fromName,
            'sent_at' => $this->sentAt,
            'from_id' => $this->fromId,
        ];
    }

    /**
     * @return array{title: string, content: string, author: string, sent_at: string, from_id: int}
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->fromName,
            'sent_at' => $this->sentAt,
            'from_id' => $this->fromId,
        ]);
    }

    /**
     * @return array{title: string, content: string, author: string, sent_at: string}
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
