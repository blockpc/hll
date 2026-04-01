<div>
    @if (!$this->countSquads)
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_infantry.no_infantry_squad') }}</div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach ($roster->infantrySquads as $infantrySquad)
            <x-squad :squad="$infantrySquad" :buttons="$displayControls" wire:key="infantry-squad-{{ $infantrySquad->id }}" />
            @endforeach
        </div>
    @endif
</div>
