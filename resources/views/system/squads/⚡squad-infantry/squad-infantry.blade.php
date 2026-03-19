<div>
    @if ($hasInfantrySquads)
        @foreach ($infantries as $infantry)
            <div class="flex flex-col space-y-1 p-1">
                <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                    <div>{{ $infantry->name }} </div>
                    <flux:button size="xs" variant="ghost">({{ $infantry->squadSoldiers->count() }}/{{ $infantry->max_members ?? 6 }}) +</flux:button>
                </div>
                @foreach ($infantry->squadSoldiers as $squadSoldier)
                    <flux:button size="sm" variant="ghost">{{ $squadSoldier->name }}</flux:button>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_infantry.no_command_squad') }}</div>
    @endif
</div>
