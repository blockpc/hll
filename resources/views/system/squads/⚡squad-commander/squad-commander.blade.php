<div class="w-full">
    <div class="grid grid-cols-1 gap-4">
        @if ($squadCommander)
            <x-squad :squad="$squadCommander" :buttons="$displayControls" wire:key="commander-squad-{{ $squadCommander->id }}" />
        @endif
    </div>
</div>
