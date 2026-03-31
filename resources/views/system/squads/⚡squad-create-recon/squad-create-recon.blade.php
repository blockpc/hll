<div>
    @if ($this->canCreate)
        <flux:modal.trigger name="create-squad-recon">
            <flux:button variant="outline" size="xs" icon="plus" />
        </flux:modal.trigger>
    @endif
    <flux:modal name="create-squad-recon" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squads.squad_recon.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squads.squad_recon.modal_subtitle') }}</flux:text>
            </div>

            <div class="space-y-2">
                <flux:input size="sm" label="{{ __('hll.squads.form.name') }}" wire:model="name" />

                <flux:input size="sm" label="{{ __('hll.squads.form.alias') }}" wire:model="alias" />

                <flux:label>{{ __('hll.squads.form.roster_type_squad') }}</flux:label>
                <flux:badge color="blue" class="w-full mb-1">
                    {{ $roster_type_squad?->label() }}
                </flux:badge>
            </div>

            <div class="flex justify-end items-center space-x-2">
                <flux:button variant="ghost" size="sm" wire:click="cancelModal">
                    {{ __('hll.commons.cancel') }}
                </flux:button>
                <flux:button variant="primary" color="blue" size="sm" wire:click="save">
                    {{ __('hll.squads.create.button') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
