<div>
    @placeholder
        <div class="space-y-4">
            <flux:skeleton class="h-12 w-full" animate="pulse" />
            <flux:skeleton class="h-6 w-full" animate="pulse" />

            <flux:separator variant="subtle" />

            <flux:skeleton class="h-screen flex items-center justify-center" animate="pulse">
                <div>{{ __('loading') }}</div>
            </flux:skeleton>
        </div>
    @endplaceholder

    <div class="flex items-start justify-between space-x-6">
        <x-header-clan :clan="$clan" :title="__('hll.clans.rosters.template.roster_title', ['name' => $roster->name])" :subtitle="__('hll.clans.rosters.template.subtitle')" />
        <div class="flex flex-col">
            <div class="flex items-center space-x-2">
                <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                    {{ __('hll.clans.rosters.back_to_clan') }}
                </flux:button>

                <flux:button variant="ghost" size="sm" href="{{ route('rosters.table', $clan->slug) }}">
                    {{ __('hll.clans.rosters.back_to_rosters') }}
                </flux:button>
            </div>
            <flux:spacer size="xs" />
            <flux:navbar>
                <flux:navbar.item variant="ghost" class="w-full" href="{{ route('rosters.template.manage', ['clan' => $clan->slug, 'roster' => $roster->uuid]) }}">{{ __('hll.clans.rosters.roster_manage') }}</flux:navbar.item>
                <flux:navbar.item variant="ghost" class="w-full" href="{{ route('rosters.template.map', ['clan' => $clan->slug, 'roster' => $roster->uuid]) }}">{{ __('hll.clans.rosters.roster_map') }}</flux:navbar.item>
            </flux:navbar>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="flex flex-col max-h-max mt-4">
        <div class="flex-1">
            <div class="col-span-5 border-b-2 border-amber-300 p-1 mb-2">
                <div class="flex items-center justify-between text-amber-500">
                    <div class="text-sm">{{ $roster->name }}</div>
                    <div class="text-sm">{{ __('hll.rosters.map') }}: {{ $roster->map->name }}</div>
                    <div class="text-sm">{{ __('hll.rosters.central_point') }}: {{ $roster->centralPoint->name }}</div>
                    <div class="text-sm">{{ __('hll.rosters.faction') }}: {{ $roster->faction->label() }}</div>
                    <div class="text-sm">{{ $roster->assignedSoldiersCount() }}/{{ $roster->max_soldiers }}</div>
                </div>
            </div>
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
                        <img src="{{ asset('images/mapa-hll.png') }}" class="w-full h-auto rounded" alt="{{ __('hll.rosters.map_alt') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
