<div>
    <flux:modal name="add-soldier" @close="$wire.cancelModal()" @cancel="$wire.cancelModal()" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('hll.squad_soldiers.add.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('hll.squad_soldiers.add.subtitle') }}</flux:text>
            </div>

            <ul class="text-sm text-gray-500">
                <li>{{ __('hll.squad_soldiers.add.requirements.same_clan') }}</li>
                <li>{{ __('hll.squad_soldiers.add.requirements.not_assigned') }}</li>
                <li>{{ __('hll.squad_soldiers.add.requirements.capacity', ['capacity' => $squad?->capacity ?? 0]) }}</li>
            </ul>

            <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.current_count', ['count' => $squad?->soldiers()->count() ?? 0]) }}</p>

            <div class="space-y-2">
                <x-toggle name="adding_many_soldiers" yes="hll.squad_soldiers.add.add_by_id" not="hll.squad_soldiers.add.add_by_name" wire:model.live="singleSelection" />

                @if ($singleSelection)
                    <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_id') }}</p>
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="flex items-center justify-between">
                            <div class="text-sm italic">{{ __('hll.clans.rosters.soldiers') }}</div>
                            <div class="text-sm italic">({{ $roster->assignedSoldiersCount() }}/{{ $roster->clan->soldiers()->count() }})</div>
                        </div>
                        <flux:error name="soldierId" class="text-sm text-red-500" />
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
                                    <div class="flex justify-between w-full">
                                        <span>{{ $soldierKeyName }}</span>
                                        @if ($soldierId === $soldierKeyId)
                                            <flux:icon name="check" class="text-green-500" variant="micro" />
                                        @endif
                                    </div>
                                </flux:button>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_name') }}</p>
                    <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_name_requirements') }}</p>
                    <flux:textarea size="sm" label="{{ __('hll.squad_soldiers.add.form.soldier_by_name') }}" wire:model="soldiersByName" />
                @endif

            </div>

            <div class="flex justify-end items-center space-x-2">
                <flux:button variant="ghost" size="sm" wire:click="cancelModal">
                    {{ __('hll.commons.cancel') }}
                </flux:button>
                <flux:button variant="primary" color="blue" size="sm" wire:click="addSoldier">
                    {{ __('hll.squad_soldiers.add.button') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
