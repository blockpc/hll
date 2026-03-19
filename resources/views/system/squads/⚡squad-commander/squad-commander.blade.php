<div class="w-full">
    @if ($squadCommander)
    <flux:button class="w-full uppercase justify-start" size="xs" icon:trailing="x-mark" wire:click="show">{{ __('hll.squads.squad_command.commander_name', ['name' => $squadCommander->name]) }}</flux:button>
    @else
    <div class="text-sm text-gray-500">{{ __('hll.squads.squad_command.no_command_squad') }}</div>
    @endif
</div>
