<div>
    @if ($this->canCreate)
        <flux:modal.trigger :name="$this->modalName()">
            <flux:button variant="outline" size="xs" icon="plus" />
        </flux:modal.trigger>

        <flux:modal :name="$this->modalName()" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ $this->title() }}</flux:heading>
                    <flux:text class="mt-2">{{ $this->modalSubtitle() }}</flux:text>
                </div>

                <div class="space-y-2">
                    <flux:input size="sm" label="{{ __('hll.squads.form.name') }}" wire:model="name" />

                    <flux:input size="sm" label="{{ __('hll.squads.form.alias') }}" wire:model="alias" />

                    <flux:label>{{ __('hll.squads.form.roster_type_squad') }}</flux:label>
                    <flux:badge color="blue" class="w-full mb-1">
                        {{ $type->label() }}
                    </flux:badge>
                </div>

                <div class="flex justify-end items-center space-x-2">
                    <flux:button variant="ghost" size="sm" wire:click="cancelModal">
                        {{ __('hll.commons.cancel') }}
                    </flux:button>
                    <flux:button variant="primary" color="blue" size="sm" wire:click="save" wire:loading.attr="disabled">
                        {{ __('hll.squads.create.button') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
