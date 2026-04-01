<div>
    @if (!$this->countSquads)
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_recon.no_recon_squad') }}</div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach ($roster->reconSquads as $reconSquad)
            <x-squad :squad="$reconSquad" :buttons="$buttons" wire:key="recon-squad-{{ $reconSquad->id }}" />
            @endforeach
        </div>
    @endif
</div>
