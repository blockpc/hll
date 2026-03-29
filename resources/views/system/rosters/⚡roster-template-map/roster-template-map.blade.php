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
        <div class="border border-dashed border-gray-300 dark:border-gray-700 flex-1">
            <div class="grid grid-cols-6 gap-4 max-h-max">
                <div class="col-span-2 border flex flex-col space-y-4 p-1">
                    <div class="flex flex-col space-y-1 p-1" id="commander-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.commander') }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1 p-1" id="infantry-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.infantry') }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1 p-1" id="armor-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.armor') }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1 p-1" id="recon-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.recon') }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1 p-1" id="artillery-section">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-sm italic border-b border-gray-500">{{ __('hll.squads.sections.artillery') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-span-4 border flex flex-col space-y-4 p-1">
                    <div class="flex items-center justify-between">
                        <div class="text-sm">{{ $roster->map?->name ?? 'N/A' }}</div>
                        <div class="text-sm">{{ $roster->centralPoint?->name ?? 'N/A' }}</div>
                        <div class="text-sm">{{ $roster->faction?->label() ?? __('hll.clans.rosters.template.no_faction') }}</div>
                    </div>
                    <div>
                        <img src="{{ asset('images/mapa-hll.png') }}" class="w-full h-auto rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
