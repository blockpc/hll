<div>
    <flux:modal name="add-soldier" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squad_soldiers.add.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squad_soldiers.add.subtitle') }}</flux:text>
            </div>

            <flux:text class="text-sm text-gray-500">
                {{ __('hll.squad_soldiers.add.requirements') }}
            </flux:text>

            <div class="space-y-2">
                <x-toggle name="findByIdOrByName" yes="hll.squad_soldiers.add.add_by_id" not="hll.squad_soldiers.add.add_by_name" wire:model.live="option" />

                @if ($option)
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="flex items-center justify-between">
                            <div class="text-sm italic">{{ __('hll.clans.rosters.soldiers') }}</div>
                            <div class="text-sm italic">({{ $roster->assignedSoldiersCount() }}/{{ $roster->clan->soldiers()->count() }})</div>
                        </div>
                        <div class="border-b pb-1">
                            <flux:input :loading="false" :clearable="true" placeholder="{{ __('hll.clans.rosters.search_soldier') }}" wire:model.live.debounce.500ms="searchSoldier" size="xs" autocomplete="off">
                                <x-slot name="icon">
                                    <flux:icon name="magnifying-glass" class="text-gray-500" variant="micro" />
                                </x-slot>
                            </flux:input>
                        </div>
                        <div class="flex flex-col space-y-1 max-h-64 overflow-y-auto overscroll-y-auto">
                            @foreach ($this->soldiers as $soldierKeyId => $soldierKeyName)
                                <flux:button variant="outline" size="xs" class="justify-start" wire:click="setSoldierId({{ $soldierKeyId }})">
                                    {{ $soldierKeyName }}
                                </flux:button>
                            @endforeach
                        </div>
                    </div>
                @else
                    <flux:input size="sm" label="{{ __('hll.squad_soldiers.add.form.soldier_by_name') }}" wire:model="soldierByName" />
                @endif

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
