<div>
    <flux:modal
        wire:model.self="open"
        name="user-notifications"
        flyout
        class="w-full max-w-64 p-2!"
        :closable="false"
    >
        <div class="flex h-full flex-col">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <flux:heading size="lg" class="font-semibold">{{ __('system.notifications.title') }}</flux:heading>
                    <flux:text class="mt-1 text-xs italic">
                    {{ trans_choice('system.notifications.counter', $this->unreadCount) }}
                    </flux:text>
                </div>

                <flux:modal.close>
                    <flux:button icon="x-mark" variant="ghost" size="sm" />
                </flux:modal.close>
            </div>

            <div class="space-y-3 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <div class="rounded-lg border p-3 opacity-70">
                        <div class="flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="grid gap-1">
                                <p class="text-sm font-semibold">
                                    {{ $notification->data['title'] }}
                                </p>
                                <p class="p-2 text-xs italic text-zinc-600 dark:text-zinc-300">
                                    {!! nl2br(e($notification->data['content'])) !!}
                                </p>
                            </div>
                            <flux:spacer />
                            <flux:button type="button" wire:click="markAsRead('{{ $notification->id }}')" variant="ghost" size="xs">
                                {{ __('mark as read') }}
                            </flux:button>
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
                    <div class="rounded-lg border border-dashed p-4 text-sm text-zinc-500">
                        {{ __('system.notifications.no_notifications') }}
                    </div>
                @endforelse
            </div>
        </div>
        <div class="top-full sticky mt-2">
            <div class="flex items-center justify-between gap-2">
                <flux:button variant="ghost" size="xs" wire:click="markAllAsRead">
                    {{ __('system.notifications.mark_all_as_read') }}
                </flux:button>
                <flux:button variant="ghost" :href="route('notifications.table')" size="xs">
                    {{ __('system.notifications.view_all') }}
                </flux:button>
            </div>
            <flux:fieldset class="mt-2 border border-gray-400 rounded p-2 text-xs">
                <flux:legend>
                    <span class="text-sm">{{ __('system.notifications.send_notification_to_user') }}</span>
                </flux:legend>

                <div class="grid gap-2">
                    <x-select2-single
                        name="users"
                        title="system.notifications.select_user"
                        :options="$this->users"
                        :selected_id="$selectedUserId"
                        :selected_name="$selectedUserName"
                        search="searchUser"
                        click="selectUser"
                    />
                    <flux:error name="selectedUserId" />
                    <flux:field>
                        <div class="flex justify-between items-center">
                            <div class="text-base">{{ __('system.notifications.title_label') }}</div>
                            <div class="text-xs italic text-yellow-500">{{ __('system.notifications.max_64_characters') }}</div>
                        </div>
                        <flux:input wire:model="title" size="sm" :placeholder="__('system.notifications.title_placeholder')"/>
                        <flux:error name="title" />
                    </flux:field>
                    <flux:field>
                        <div class="flex justify-between items-center">
                            <div class="text-base">{{ __('system.notifications.content_label') }}</div>
                            <div class="text-xs italic text-yellow-500">{{ __('system.notifications.max_255_characters') }}</div>
                        </div>
                        <flux:textarea wire:model="content" :placeholder="__('system.notifications.content_placeholder')" resize="none" />
                        <flux:error name="content" />
                    </flux:field>
                    <flux:button variant="primary" color="blue" size="sm" class="w-full" wire:click="send">{{ __('system.notifications.send_notification_to_user') }}</flux:button>
                </div>
            </flux:fieldset>
        </div>
    </flux:modal>
</div>
