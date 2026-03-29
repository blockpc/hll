<div class="w-full">
    @if ($squadCommander)
        <x-squad :squad="$squadCommander" :buttons="true" />
    @else
        <flux:badge>
            {{ __('hll.squads.squad_command.no_command_squad_badge') }}
        </flux:badge>
    @endif
</div>
