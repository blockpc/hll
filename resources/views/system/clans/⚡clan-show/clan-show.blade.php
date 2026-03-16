<div class="w-full">
    <div class="relative mb-6 w-full">
        <div class="flex items-start justify-between space-x-6">
            <div class="flex space-x-6">
                <div>
                    @if ($clan->logo)
                        <img src="{{ $clan->logo_url }}" alt="{{ $clan->name }}" class="h-16 w-16 rounded-full object-cover">
                    @else
                        <x-placeholder-pattern class="h-16 w-16 rounded-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    @endif
                </div>
                <div>
                    <flux:heading size="xl" level="1">{{ $clan->alias }} | {{ $clan->name }}</flux:heading>
                    <flux:subheading size="lg" class="mb-6">{{ $clan->description ?? __('hll.clans.show.no_description') }}</flux:subheading>
                </div>
            </div>
            <div>
                @can('update', $clan)
                <flux:button variant="primary" color="green" size="sm" icon="pencil" href="{{ route('clans.edit', $clan->slug) }}">
                    {{ __('hll.clans.show.edit_clan') }}
                </flux:button>
                @endcan
            </div>
        </div>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-col space-y-2" x-data="{show: 'helpers'}">
        <div class="grid grid-cols-3 gap-6">
            <div class="flex items-center space-x-2">
                <flux:button variant="outline" color="blue" size="sm" class="w-full" @click="show = 'helpers'">
                    {{ __('hll.clans.show.tabs.helpers') }}
                </flux:button>
                @can('update', $clan)
                <flux:button variant="primary" color="green" icon="pencil" size="xs" href="{{ route('clans.helpers.manager', $clan->slug) }}" />
                @endcan
            </div>
            <div class="flex items-center space-x-2">
                <flux:button variant="outline" color="blue" size="sm" class="w-full" @click="show = 'soldiers'">
                    {{ __('hll.clans.show.tabs.soldiers') }}
                </flux:button>
                @can('update', $clan)
                <flux:button variant="primary" color="green" icon="pencil" size="xs" href="{{ route('clans.soldiers.manager', $clan->slug) }}" />
                @endcan
            </div>
            <div class="flex items-center space-x-2">
                <flux:button variant="outline" color="blue" size="sm" class="w-full" @click="show = 'rosters'">
                    {{ __('hll.clans.show.tabs.rosters') }}
                </flux:button>
            </div>
        </div>

        <div x-show="show === 'helpers'" x-cloak>
            <flux:card class="p-2.5 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">{{ __('hll.clans.show.titles.helpers') }}</flux:heading>
                        <flux:text class="mt-2">{{ trans_choice('hll.clans.show.titles.helpers_count', $this->members->count()) }}</flux:text>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        @forelse ($this->members as $member)
                            <flux:card class="p-2.5">
                                <div class="space-y-4">
                                    <flux:heading size="base">{{ $member->name }}</flux:heading>
                                    <flux:text class="text-xs italic">{{ $member->pivot->membership_role->label() }}</flux:text>
                                </div>
                            </flux:card>
                        @empty
                            <flux:text class="text-center text-gray-500">{{ __('hll.clans.managers.no_helpers') }}</flux:text>
                        @endforelse
                    </div>
                </div>
            </flux:card>
        </div>

        <div x-show="show === 'soldiers'" x-cloak>
            <flux:card class="p-2.5 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">{{ __('hll.clans.show.titles.soldiers') }}</flux:heading>
                        <flux:text class="mt-2">{{ trans_choice('hll.clans.show.titles.soldiers_count', $this->soldiers->count()) }}</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse ($this->soldiers as $soldier)
                        <flux:card class="p-2.5 space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:heading size="base">{{ $soldier->name }}</flux:heading>
                                <flux:text class="text-xs italic">{{ $soldier->role?->label() ?? __('hll.clans.soldiers.no_role') }}</flux:text>
                            </div>
                            <flux:text class="text-xs italic">{{ $soldier->observation ?? __('hll.clans.soldiers.no_observation') }}</flux:text>
                        </flux:card>
                    @empty
                        <flux:text class="text-center text-gray-500">{{ __('hll.clans.soldiers.no_soldiers') }}</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>

        <div x-show="show === 'rosters'" x-cloak>
            <flux:card class="p-2.5 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">{{ __('hll.clans.show.titles.rosters') }}</flux:heading>
                        <flux:text class="mt-2">{{ trans_choice('hll.clans.show.titles.rosters_count', $this->rosters->count()) }}</flux:text>
                    </div>

                    <flux:button variant="outline" color="blue" size="sm">
                        {{ __('hll.clans.rosters.create') }}
                    </flux:button>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse ($this->rosters as $roster)
                        <flux:card class="p-2.5 space-y-4">
                            {{ __('loading') }}
                        </flux:card>
                    @empty
                        <flux:text class="text-center text-gray-500">{{ __('hll.clans.rosters.no_rosters') }}</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
</div>
