<div>
    @if (!$this->countSquads)
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_artillery.no_artillery_squad') }}</div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($roster->artillerySquads as $artillerySquad)
            <x-squad :squad="$artillerySquad" :buttons="$displayControls" wire:key="artillery-squad-{{ $artillerySquad->id }}" />
            @endforeach
        </div>
    @endif
</div>
