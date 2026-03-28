@props([
    'squad',
    'buttons' => false,
])

<div class="rounded border border-amber-500/20 bg-amber-500/5 p-1">
    <div class="mb-1 flex items-center justify-between">
        <h4 class="text-sm font-black uppercase tracking-wider text-amber-200">
            {{ $squad->name }}
        </h4>
        <div class="flex items-center space-x-1">
            <span class="rounded border border-amber-500/30 px-2 py-1 text-[10px] font-bold uppercase text-amber-300">
                {{ $squad->alias }}
            </span>
            @if ($buttons)
            <flux:button variant="outline" size="xs" icon="plus" wire:click="addSoldier({{ $squad->id }})" />
            <flux:button variant="outline" size="xs" icon="trash" />
            @endif
        </div>
    </div>
    <div class="flex flex-col space-y-1">
        @forelse ($squad->soldiers as $soldier)
            <div class="flex justify-between items-center rounded-md border border-white/10 bg-black/20 p-1">
            <div>{{ $soldier->display_name }}</div>
            @if ($buttons)
            <flux:button size="xs" icon:trailing="x-mark" wire:click="remove_soldier({{ $soldier->id }})" aria-label="{{ __('hll.squads.remove_soldier') }}" />
            @endif
            </div>
        @empty
            <div class="text-sm text-gray-400">{{ __('hll.squads.no_soldiers_assigned') }}</div>
        @endforelse
    </div>
</div>
