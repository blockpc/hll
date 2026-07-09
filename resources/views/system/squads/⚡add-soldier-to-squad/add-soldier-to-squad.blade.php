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

            <div class="flex flex-col space-y-2 p-1">
                <div class="flex items-center space-x-4">
                    <h2>{{ $squad?->name }}</h2>
                    <span class="text-sm text-gray-500">({{ __('hll.squad_soldiers.add.current_count', ['count' => $squad?->soldiers?->count() ?? 0]) }})</span>
                </div>
                <div class="">
                    @foreach ($squad?->soldiers ?? [] as $soldier)
                        <flux:badge size="xs" color="gray">
                            {{ $soldier->display_name }}
                        </flux:badge>
                    @endforeach
                </div>
            </div>

            @if (!$squadFull)
            <div class="space-y-2">
                <x-toggle name="adding_many_soldiers" yes="hll.squad_soldiers.add.add_by_id" not="hll.squad_soldiers.add.add_by_name" wire:model.live="singleSelection" />

                @if ($singleSelection)
                    <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_id') }}</p>
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="flex items-center justify-between">
                            <div class="text-sm italic">{{ __('hll.clans.soldiers.list') }}</div>
                            <div class="text-sm italic">({{ $roster?->assignedSoldiersFromClanCount() ?? 0 }}/{{ $roster?->clan?->soldiers()->count() ?? 0 }})</div>
                        </div>
                        <flux:error name="soldierId" class="text-sm text-red-500" />
                        <div class="border-b pb-1">
                            <flux:input :loading="false" :clearable="true" placeholder="{{ __('hll.clans.rosters.search_soldier') }}" wire:model.live.debounce.500ms="searchSoldier" size="xs" autocomplete="off">
                                <x-slot name="icon">
                                    <flux:icon name="magnifying-glass" class="text-gray-500" variant="micro" />
                                </x-slot>
                            </flux:input>
                        </div>
                        <div class="grid gap-1 max-h-64 scrollbar-always overflow-y-auto p-1">
                            @foreach ($this->soldiers as $soldierKeyId => $soldierKeyName)
                                @if (in_array($soldierKeyId, $selectedSoldiers, true))
                                <flux:button variant="outline" size="xs" class="btn-success" wire:click="setSoldierId({{ $soldierKeyId }})" :loading="false">
                                    <div class="flex justify-between w-full">
                                        <span>{{ $soldierKeyName }}</span>
                                        <flux:icon name="check" class="text-green-500" variant="micro" />
                                    </div>
                                </flux:button>
                                @elseif (in_array($soldierKeyId, $soldiersAddedRoster, true))
                                <flux:badge variant="primary" size="xs" class="justify-start btn-secondary">
                                    <div class="flex justify-between w-full">
                                        <span class="text-xs">{{ $soldierKeyName }}</span>
                                        <flux:icon name="check" class="text-green-500" variant="micro" />
                                    </div>
                                </flux:badge>
                                @else
                                <flux:button variant="outline" size="xs" class="justify-start" wire:click="setSoldierId({{ $soldierKeyId }})" :loading="false">
                                    <div class="flex justify-between w-full">
                                        <span>{{ $soldierKeyName }}</span>
                                    </div>
                                </flux:button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_name') }}</p>
                    <p class="text-sm text-gray-500">{{ __('hll.squad_soldiers.add.requirements.by_name_requirements') }}</p>
                    <flux:textarea size="sm" label="{{ __('hll.squad_soldiers.add.form.soldier_by_name') }}" wire:model="soldiersByName" />
                @endif
            </div>
            @else
            <flux:callout variant="warning">
                {{ __('hll.squads.full_squad', ['name' => $squad?->roster_type_squad?->label()]) }}
            </flux:callout>
            @endif

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
                    @if (!$squadFull)
                    <flux:button variant="primary" color="blue" size="sm" wire:click="addSoldier">
                        {{ __('hll.squad_soldiers.add.button') }}
                    </flux:button>
                    @endif
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
