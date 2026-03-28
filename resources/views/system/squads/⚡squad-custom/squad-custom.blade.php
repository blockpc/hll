<div>
    @if ($this->customSquads->isEmpty())
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_custom.no_command_squad') }}</div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach ($this->customSquads as $customSquad)
            <x-squad :squad="$customSquad" :buttons="$buttons" />
            @endforeach
        </div>
    @endif
</div>
