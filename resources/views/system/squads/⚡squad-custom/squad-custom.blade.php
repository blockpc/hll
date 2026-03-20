<div>
    @if ($this->customSquads->isEmpty())
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_custom.no_command_squad') }}</div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach ($this->customSquads as $customSquad)
            <div class="bg-gray-600 rounded">
                <div class="flex items-center justify-between space-x-2 p-1 bg-gray-600 rounded">
                    <div class="text-sm font-medium">{{ $customSquad->name }}</div>
                    <div class="flex items-center space-x-1">
                        @if ($buttons)
                        <flux:button variant="outline" size="xs" icon="plus" wire:click="addSoldier({{ $customSquad->id }})" />
                        <flux:button variant="outline" size="xs" icon="trash" />
                        <flux:button variant="outline" size="xs" icon="chevron-up" />
                        @endif
                    </div>
                </div>
                <div class="flex flex-col space-y-1 p-1 bg-gray-700">
                    @forelse ($customSquad->soldiers as $soldier)
                        <div>{{ $soldier->display_name }}</div>
                    @empty
                        <div class="text-sm text-gray-400">{{ __('hll.squads.no_soldiers_assigned') }}</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
