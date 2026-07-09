<div>
    <flux:modal name="add-soldier-custom-squad" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squad_soldiers.add.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squad_soldiers.add.subtitle') }}</flux:text>
            </div>

            <h2>{{ $squad?->name }}</h2>

            <div class="space-y-2">
                <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_name') }}</p>
                <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_name_requirements') }}</p>
                <flux:textarea size="sm" label="{{ __('hll.squad_soldiers.add.form.soldier_by_name') }}" wire:model="soldiersByName" />
            </div>

            <div class="flex justify-between items-center space-x-2">
                <div class="flex justify-start items-center space-x-2">
                    @if ($squad)
                    <flux:modal.trigger name="delete-squad-{{ $squad?->id }}">
                        <flux:button variant="danger" size="sm">{{ __('hll.squads.delete.title') }}</flux:button>
                    </flux:modal.trigger>
                    @endif
                </div>
                <div class="flex justify-end items-center space-x-2">
                    <flux:button variant="ghost" size="sm" wire:click="cancelModal">
                        {{ __('hll.commons.cancel') }}
                    </flux:button>
                    <flux:button variant="primary" color="blue" size="sm" wire:click="save">
                        {{ __('hll.squad_soldiers.add.button') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    @if ($squad)
    <flux:modal name="delete-squad-{{ $squad->id }}" @close="$wire.cancelDeleteSquad()" @cancel="$wire.cancelDeleteSquad()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squads.delete.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squads.delete.confirmation_message') }}</flux:text>
            </div>

            <div class="flex justify-end items-center space-x-2">
                <flux:button variant="ghost" size="sm" wire:click="cancelDeleteSquad">
                    {{ __('hll.commons.cancel') }}
                </flux:button>
                <flux:button variant="danger" size="sm" wire:click="deleteSquad({{ $squad->id }})">
                    {{ __('hll.squads.delete.button') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
