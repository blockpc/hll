@php use App\Enums\RosterTypeSquadEnum; @endphp
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
        <div class="border-dashed border-gray-300 dark:border-gray-700 flex-1">
            <div class="grid grid-cols-6 gap-4 max-h-max">
                <div class="col-span-5 border flex flex-col space-y-4 p-1">

                    <div class="grid grid-cols-3 gap-4">
                        <div class="flex flex-col space-y-1 p-1" id="commander-section">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.commander') }}</div>
                                <livewire:system::squads.squad-create-commander :roster="$roster" :soldiers="$this->soldiers" />
                            </div>
                            <livewire:system::squads.squad-commander :roster="$roster" :key="'commander-'.$roster->uuid" />
                        </div>
                        <div class="col-span-2 flex flex-col space-y-1 p-1" id="recon-section">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.recon') }}</div>
                                <livewire:system::squads.squad-create-typed :roster="$roster" :type="RosterTypeSquadEnum::Recon" />
                            </div>
                            <livewire:system::squads.squad-recon :roster="$roster" :key="'recon-'.$roster->uuid" />
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1 p-1" id="infantry-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.infantry') }}</div>
                            <livewire:system::squads.squad-create-typed :roster="$roster" :type="RosterTypeSquadEnum::Infantry" />
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <livewire:system::squads.squad-infantry :roster="$roster" :key="'infantry-'.$roster->uuid" />
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1 p-1" id="armor-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.armor') }}</div>
                            <livewire:system::squads.squad-create-typed :roster="$roster" :type="RosterTypeSquadEnum::Armor" />
                        </div>
                        <livewire:system::squads.squad-armor :roster="$roster" :key="'armor-'.$roster->uuid" :buttons="true" />
                    </div>

                    <div class="flex flex-col space-y-1 p-1" id="artillery-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.artillery') }}</div>
                            <livewire:system::squads.squad-create-typed :roster="$roster" :type="RosterTypeSquadEnum::Artillery" />
                        </div>
                        <livewire:system::squads.squad-artillery :roster="$roster" :key="'artillery-'.$roster->uuid" :buttons="true" />
                    </div>

                    <div class="flex flex-col space-y-1 p-1" id="custom-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.custom') }}</div>
                            <flux:button variant="outline" size="xs" icon="plus" />
                        </div>
                        <livewire:system::squads.squad-custom :roster="$roster" :key="'custom-'.$roster->uuid" :buttons="true" />
                    </div>
                </div>

                <div class="col-span-1 border flex flex-col space-y-4 p-1">
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="text-sm italic border-b">{{ __('hll.clans.rosters.commands') }}</div>
                        @foreach ($this->typeSquads as $typeSquad)
                            <flux:button variant="primary" size="xs" color="{{ $typeSquad->color() }}" wire:click="createSquad('{{ $typeSquad->value }}')">{{ $typeSquad->label() }}</flux:button>
                        @endforeach
                    </div>
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="flex items-center justify-between">
                            <div class="text-sm italic">{{ __('hll.clans.rosters.soldiers') }}</div>
                            <div class="text-sm italic">({{ count($selectedSoldiers) }}/{{ $clan->soldiers->count() }})</div>
                        </div>
                        <div class="border-b pb-1">
                            <flux:input :loading="false" :clearable="true" placeholder="{{ __('hll.clans.rosters.search_soldier') }}" wire:model.live.debounce.500ms="searchSoldier" size="xs" autocomplete="off">
                                <x-slot name="icon">
                                    <flux:icon name="magnifying-glass" class="text-gray-500" variant="micro" />
                                </x-slot>
                            </flux:input>
                        </div>
                        <div class="flex flex-col space-y-1 max-h-64 overflow-y-auto overscroll-y-auto">
                            @foreach ($this->soldiers as $soldierId => $soldierName)
                                <flux:button variant="outline" size="xs" class="justify-start">
                                    {{ $soldierName }}
                                </flux:button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:system::squads.squad-create :roster="$roster" />
    <livewire:system::squads.add-soldier-to-squad :roster="$roster" />
</div>
