<div class="w-full">
    @if ($squadCommander)
        <x-squad :squad="$squadCommander" :buttons="true" />
    @else
        <div class="text-sm text-gray-500">{{ __('hll.squads.squad_command.no_command_squad') }}</div>
    @endif
</div>
