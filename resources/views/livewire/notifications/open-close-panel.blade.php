<div>
    <flux:button wire:click="$dispatch('open-notifications')" icon="bell" class="relative w-full">
        Notificaciones
        @if($unreadCount > 0)
            <span class="absolute -right-1 -top-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white">
                {{ $unreadCount }}
            </span>
        @endif
    </flux:button>
</div>
