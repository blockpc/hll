<div class="w-full">
    @if ($squadCommander)
    <div class="rounded border border-amber-500/20 bg-amber-500/5 p-1">
        <div class="mb-1 flex items-center justify-between">
            <h4 class="text-sm font-black uppercase tracking-wider text-amber-200">
                {{ $squadCommander->name }}
            </h4>
            <span class="rounded border border-amber-500/30 px-2 py-1 text-[10px] font-bold uppercase text-amber-300">
                {{ $squadCommander->alias }}
            </span>
        </div>

        <div class="flex justify-between items-center rounded-md border border-white/10 bg-black/20 p-1">
            <div class="text-sm text-zinc-200">
                {{ __('hll.squads.squad_command.commander_name', ['name' => $squadCommander->soldiers->first()?->display_name ?? __('hll.squads.squad_command.no_command_squad')]) }}
            </div>
            <flux:button size="xs" icon:trailing="x-mark" wire:click="show" aria-label="{{ __('hll.squads.squad_command.remove_commander') }}" />
        </div>
    </div>
    @else
    <div class="text-sm text-gray-500">{{ __('hll.squads.squad_command.no_command_squad') }}</div>
    @endif
</div>
