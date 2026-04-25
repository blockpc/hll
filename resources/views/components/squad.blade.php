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
            @else
            <flux:button variant="outline" size="xs" class="rounded border text-amber-300 border-amber-500/30! bg-amber-500/30! uppercase text-[10px] font-bold">
                {{ $squad->alias }}
            </flux:button>
            @endif
        </div>
    </div>
    <ul class="flex flex-col">
        @forelse ($squad->soldiers as $soldier)
            <li class="flex justify-between items-center p-1 hover:bg-amber-500/10 h-8 transition group {{ $loop->first ? 'border border-white/10 bg-amber-400/20' : 'bg-black/20' }}">
                <div class="text-xs" title="{{ $soldier->display_name }}">{{ Str::limit($soldier->display_name, 15, '...') }}</div>
                @if ($buttons)
                <div class="group-hover:block hidden">
                    <flux:button type="button" variant="outline" size="xs" wire:click="removeSoldier({{ $soldier->id }})" aria-label="{{ __('hll.squads.remove_soldier') }}" class="rounded border border-red-500/30! bg-red-500/30! text-[10px] p-1!">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </flux:button>
                </div>
                @endif
            </li>
        @empty
            <li class="text-sm text-gray-400">{{ __('hll.squads.no_soldiers_assigned') }}</li>
        @endforelse
    </ul>
</div>
