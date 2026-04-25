<div>
    <section class="mx-auto max-w-7xl grid gap-4 py-4" x-data="{tab: 'template'}">
        <div class="relative w-full mb-4">
            <div class="flex space-x-6 h-24">
                <div class="flex">
                    @if ($roster->clan?->logo)
                        <img src="{{ $roster->clan->logo_url }}" alt="{{ $roster->clan->name }}" class="h-24 w-24 rounded-full object-cover">
                    @else
                        <div class="relative w-24 h-24 rounded-full flex items-center justify-center">
                            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20 rounded-full border border-amber-500/30" />
                            <span class="text-gray-300 text-lg font-semibold">{{ $roster->clan->alias }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col justify-between">
                    <div class="text-3xl font-bold">{{ __('hll.clans.rosters.template.roster_title', ['name' => $roster->name]) }}</div>
                    <div class="text-xl font-semibold">{{ $roster->clan->alias }} | {{ $roster->clan->name }}</div>
                    @if ($roster->description)
                    <div class="text-sm italic">{{ $roster->description ?? '' }}</div>
                    @endif
                </div>
                <div class="flex items-center space-x-4" role="tablist">
                    <flux:button
                        variant="primary"
                        class="w-full"
                        x-bind:class="tab === 'template' ? 'rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20' : 'rounded-md border border-white/15 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-white/25 hover:bg-white/5'"
                        x-bind:aria-selected="tab === 'template'"
                        size="sm"
                        @click="tab = 'template'"
                        role="tab"
                        aria-controls="roster-section"
                    >
                        {{ __('rosters.view_roster_public') }}
                    </flux:button>

                    <flux:button
                        variant="primary"
                        class="w-full"
                        x-bind:class="tab === 'map' ? 'rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20' : 'rounded-md border border-white/15 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-white/25 hover:bg-white/5'"
                        x-bind:aria-selected="tab === 'map'"
                        size="sm"
                        @click="tab = 'map'"
                        role="tab"
                        aria-controls="map-section"
                    >
                        {{ __('rosters.view_map_public') }}
                    </flux:button>
                </div>
            </div>
        </div>
        <div class="flex flex-col space-y-4">
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
    </section>
</div>
