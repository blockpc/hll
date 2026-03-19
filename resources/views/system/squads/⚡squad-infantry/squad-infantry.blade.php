<div>
    @if ($infantrySquads)
        @foreach ($infantries as $infantry)
            <div class="flex flex-col space-y-1 p-1">
                <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                    <div>{{ $infantry->name }} </div>
                    <flux:button size="xs" variant="ghost">(3/6) +</flux:button>
                </div>
                {{-- TODO: Replace with actual infantry member data --}}
                @foreach ($infantry->members as $member)
                    <flux:button size="sm" variant="ghost">{{ $member->name }}</flux:button>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_infantry.no_command_squad') }}</div>
    @endif
</div>
