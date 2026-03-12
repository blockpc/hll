<div>
    <div class="w-full space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">{{ __('system.notifications.title') }}</h1>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <flux:card size="sm" class="flex justify-between items-center">
                <flux:heading size="lg">{{ __('system.notifications.notifications_all') }}</flux:heading>
                <flux:badge rounded color="blue">{{ $allCount }}</flux:badge>
            </flux:card>

            <flux:card size="sm" class="flex justify-between items-center">
                <flux:heading size="lg">{{ __('system.notifications.notifications_unread') }}</flux:heading>
                <flux:badge rounded color="yellow">{{ $unreadCount }}</flux:badge>
            </flux:card>

            <flux:card size="sm" class="flex justify-between items-center">
                <flux:heading size="lg">{{ __('system.notifications.notifications_read') }}</flux:heading>
                <flux:badge rounded color="green">{{ $readCount }}</flux:badge>
            </flux:card>
        </div>

        <hr class="my-4" />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($this->notifications as $notification)
                <div class="rounded-lg border p-3 opacity-70">
                    <div class="flex flex-col h-full gap-2">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-black dark:text-white font-bold">
                                {{ $notification->data['title'] }}
                            </div>
                            <div>
                                @if (!$notification->read_at)
                                    <flux:button variant="ghost" size="xs" wire:click="markAsRead('{{ $notification->id }}')">
                                        {{ __('system.notifications.mark_as_read') }}
                                    </flux:button>
                                @else
                                    <span class="text-xs italic">
                                        {{  __('system.notifications.read_at', ['date' => $notification->read_at->format('d/m/Y H:i')]) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="p-2 text-xs italic text-zinc-600 dark:text-zinc-300">
                                {!! nl2br(e($notification->data['content'])) !!}
                            </p>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold">
                                {{ __('system.notifications.from') }}: {{ $notification->data['author'] }}
                            </div>
                            <div class="text-xs font-semibold">
                                {{ $notification->data['sent_at'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="mt-4 p-4 col-span-full text-sm text-neutral-600 dark:text-neutral-400">{{ __('system.notifications.no_notifications') }}</p>
            @endforelse
        </div>
        <div>
            <flux:pagination :paginator="$this->notifications" />
        </div>
    </div>
</div>
