<div>
    @if (!$this->commandSquads)
        <flux:modal.trigger name="create-squad-command">
            <flux:button variant="outline" size="xs" icon="plus" />
        </flux:modal.trigger>
    @else
        <flux:button variant="outline" size="xs" icon="x-mark" disabled />
    @endif
    <flux:modal name="create-squad-command" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squads.squad_command.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squads.squad_command.subtitle') }}</flux:text>
            </div>

            <flux:text class="mt-2">{{ __('hll.squads.squad_command.requirements') }}</flux:text>

            <flux:input size="sm" label="{{ __('hll.squads.form.name') }}" wire:model="name" />

            <flux:input size="sm" label="{{ __('hll.squads.form.alias') }}" wire:model="alias" />

            <div class="flex flex-col space-y-1 max-h-64 overflow-y-auto overscroll-y-auto">
                @forelse ($soldiers as $soldierId => $soldierName)
                    @if ($selectedSoldierId !== $soldierId)
                        <flux:button size="sm" class="justify-start" wire:click="addCommander({{ $soldierId }})">
                            {{ $soldierName }}
                        </flux:button>
                    @else
                        <flux:button variant="primary" size="sm" class="justify-start" disabled color="blue">
                            {{ $soldierName }}
                        </flux:button>
                    @endif
                @empty
                    <flux:text class="text-sm text-zinc-500">{{ __('hll.squads.squad_command.no_soldiers') }}</flux:text>
                @endforelse
            </div>

            <flux:error name="selectedSoldierId" />

            <div class="flex justify-end items-center space-x-2">
                <flux:button variant="ghost" size="sm" wire:click="cancelModal">
                    {{ __('hll.commons.cancel') }}
                </flux:button>
                <flux:button variant="primary" color="blue" size="sm" wire:click="save">
                    {{ __('hll.squads.squad_command.button') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
