<div>
    @if (!$this->countSquads)
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_armor.no_armor_squad') }}</div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach ($roster->armorSquads as $armorSquad)
            <x-squad :squad="$armorSquad" :buttons="$buttons" wire:key="armor-squad-{{ $armorSquad->id }}" />
            @endforeach
        </div>
    @endif
</div>
