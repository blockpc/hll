<div>
    @include('partials.rosters-header')

    <flux:separator variant="subtle" />

    <div x-data="{tab: 'template'}" class="flex flex-col space-y-4">
        <div class="w-full flex items-center space-x-4 mt-2" role="tablist">
            <flux:button
                variant="ghost"
                class="w-full"
                x-bind:class="tab === 'template' ? 'btn-default' : ''"
                x-bind:aria-selected="tab === 'template'"
                size="sm"
                @click="tab = 'template'"
                role="tab"
                aria-controls="roster-section"
            >
                {{ __('hll.clans.rosters.template.template_tab') }}
            </flux:button>

            <flux:button
                variant="ghost"
                class="w-full"
                x-bind:class="tab === 'map' ? 'btn-default' : ''"
                x-bind:aria-selected="tab === 'map'"
                size="sm"
                @click="tab = 'map'"
                role="tab"
                aria-controls="map-section"
            >
                {{ __('hll.clans.rosters.template.map_tab') }}
            </flux:button>
        </div>
        <div class="flex flex-col max-h-max">
            <div class="col-span-5 border-b-2 border-amber-300 p-1 mb-2">
                <div class="flex items-center justify-between text-amber-500">
                    <div class="text-sm">{{ $roster->name }}</div>
                    <div class="text-sm">{{ __('hll.rosters.map') }}: {{ $roster->map?->name ?? '-' }}</div>
                    <div class="text-sm">{{ __('hll.rosters.central_point') }}: {{ $roster->centralPoint?->name ?? '-' }}</div>
                    <div class="text-sm">{{ __('hll.rosters.faction') }}: {{ $roster->faction?->label() ?? '-' }}</div>
                    <div class="text-sm">{{ $roster->assignedSoldiersCount() }}/{{ $roster->max_soldiers }}</div>
                </div>
            </div>
            <div class="border-dashed border-gray-300 dark:border-gray-700 flex-1">
                <div class="grid gap-4 max-h-max">
                    <div class="border-dashed border-gray-300 flex flex-col space-y-4"
                        id="roster-section"
                        role="tabpanel"
                        tabindex="0"
                        x-show="tab === 'template'"
                        x-cloak
                    >
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="flex flex-col space-y-1 p-1" id="commander-section">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.commander') }}</div>
                                </div>
                                <livewire:system::squads.squad-commander :roster="$roster" :key="'commander-'.$roster->uuid" :displayControls="false" />
                            </div>
                            <div class="flex flex-col space-y-1 p-1" id="recon-section">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.recon') }}</div>
                                </div>
                                <livewire:system::squads.squad-recon :roster="$roster" :key="'recon-'.$roster->uuid" :displayControls="false" />
                            </div>
                            <div class="flex flex-col space-y-1 p-1" id="artillery-section">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.artillery') }}</div>
                                </div>
                                <livewire:system::squads.squad-artillery :roster="$roster" :key="'artillery-'.$roster->uuid" :displayControls="false" />
                            </div>
                        </div>

                        <div class="flex flex-col space-y-1 p-1" id="infantry-section">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.infantry') }}</div>
                            </div>
                            <livewire:system::squads.squad-infantry :roster="$roster" :key="'infantry-'.$roster->uuid" :displayControls="false" />
                        </div>

                        <div class="flex flex-col space-y-1 p-1" id="armor-section">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.armor') }}</div>
                            </div>
                            <livewire:system::squads.squad-armor :roster="$roster" :key="'armor-'.$roster->uuid" :displayControls="false" />
                        </div>

                        <div class="flex flex-col space-y-1 p-1" id="custom-section">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.custom') }}</div>
                            </div>
                            <livewire:system::squads.squad-custom :roster="$roster" :key="'custom-'.$roster->uuid" :displayControls="false" />
                        </div>
                    </div>
                    <div class="border flex flex-col space-y-4 p-1"
                        id="map-section"
                        role="tabpanel"
                        tabindex="0"
                        x-show="tab === 'map'"
                        x-cloak
                    >
                        <div class="grid grid-cols-6 gap-4 max-h-max">
                            <div class="col-span-2 border flex flex-col space-y-4 p-1">
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="grid grid-cols-2 gap-2" id="commander-section-alias">
                                        <div class="col-span-2 flex justify-between items-start">
                                            <div class="flex-1 text-xs italic border-b border-gray-500">{{ __('hll.squads.sections.commander') }}</div>
                                        </div>
                                        @forelse ($roster->commandSquads as $commandSquad)
                                            <x-squad-alias :squad="$commandSquad" />
                                        @empty
                                        @endforelse
                                    </div>
                                    <div class="grid grid-cols-2 gap-2" id="recon-section-alias">
                                        <div class="col-span-2 flex justify-between items-start">
                                            <div class="flex-1 text-xs italic border-b border-gray-500">{{ __('hll.squads.sections.recon') }}</div>
                                        </div>
                                        @forelse ($roster->reconSquads as $reconSquad)
                                            <x-squad-alias :squad="$reconSquad" />
                                        @empty
                                        @endforelse
                                    </div>
                                    <div class="grid grid-cols-2 gap-2" id="artillery-section-alias">
                                        <div class="col-span-2 flex justify-between items-start">
                                            <div class="flex-1 text-xs italic border-b border-gray-500">{{ __('hll.squads.sections.artillery') }}</div>
                                        </div>
                                        @forelse ($roster->artillerySquads as $artillerySquad)
                                            <x-squad-alias :squad="$artillerySquad" />
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                                <div class="grid grid-cols-6 gap-2" id="infantry-section-alias">
                                    <div class="col-span-6 flex justify-between items-start">
                                        <div class="flex-1 text-xs italic border-b border-gray-500">{{ __('hll.squads.sections.infantry') }}</div>
                                    </div>
                                    @forelse ($roster->infantrySquads as $infantrySquad)
                                        <x-squad-alias :squad="$infantrySquad" />
                                    @empty
                                    @endforelse
                                </div>
                                <div class="grid grid-cols-6 gap-2" id="armor-section-alias">
                                    <div class="col-span-6 flex justify-between items-start">
                                        <div class="flex-1 text-xs italic border-b border-gray-500">{{ __('hll.squads.sections.armor') }}</div>
                                    </div>
                                    @forelse ($roster->armorSquads as $armorSquad)
                                        <x-squad-alias :squad="$armorSquad" />
                                    @empty
                                    @endforelse
                                </div>
                                <div class="grid grid-cols-6 gap-2" id="custom-section-alias">
                                    <div class="col-span-6 flex justify-between items-start">
                                        <div class="flex-1 text-xs italic border-b border-gray-500">{{ __('hll.squads.sections.custom') }}</div>
                                    </div>
                                    @forelse ($roster->customSquads as $customSquad)
                                        <x-squad-alias :squad="$customSquad" />
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                            <div class="col-span-4 border flex flex-col space-y-4 p-1">
                                <div>
                                    <img src="{{ $roster->imageUrl ?? asset('images/mapa-hll.png') }}" class="w-full h-auto rounded" alt="{{ __('hll.rosters.map_alt') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
