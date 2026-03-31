
<div>
    <flux:modal name="create-squad" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squads.create.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squads.create.subtitle') }}</flux:text>
            </div>

            <div class="space-y-2">
                <flux:error name="squad_limit_reached" class="text-sm text-red-500" />

                <flux:input size="sm" label="{{ __('hll.squads.form.name') }}" wire:model="name" />

                <flux:input size="sm" label="{{ __('hll.squads.form.alias') }}" wire:model="alias" />

                <flux:select size="sm" label="{{ __('hll.squads.form.roster_type_squad') }}" wire:model="roster_type_squad">
                    <option value="">{{ __('hll.squads.create.select_type') }}</option>
                    @foreach ($this->typeSquads as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </flux:select>
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
