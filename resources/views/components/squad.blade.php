@props([
    'squad',
    'buttons' => false,
])

<div class="rounded border border-amber-500/20 bg-amber-500/5 p-1">
    <div class="mb-1 flex items-center justify-between">
        <h4 class="text-xs font-black uppercase tracking-wider text-amber-200">
            {{ $squad->name }}
        </h4>
        <div class="flex items-center space-x-1">
            @if ($buttons)
            <flux:button variant="outline" size="xs" wire:click="openAddSoldierModal({{ $squad->id }})" class="rounded border text-amber-300 border-amber-500/30! bg-amber-500/30! uppercase text-[10px] font-bold">
                {{ $squad->alias }}
            </flux:button>
            @endif
        </div>
    </div>
    <div class="flex flex-col">
        @forelse ($squad->soldiers as $soldier)
            <div class="flex justify-between items-center rounded-md border border-white/10 bg-black/20 p-1">
                <div class="text-xs">{{ $soldier->display_name }}</div>
                @if ($buttons)
                    <flux:button type="button" variant="outline" size="xs" wire:click="removeSoldier({{ $soldier->id }})" aria-label="{{ __('hll.squads.remove_soldier') }}" class="rounded border border-red-500/30! bg-red-500/30! text-[10px] p-1!">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </flux:button>
                @endif
            </div>
        @empty
            <div class="text-sm text-gray-400">{{ __('hll.squads.no_soldiers_assigned') }}</div>
        @endforelse
    </div>
</div>
