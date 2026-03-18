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
                <flux:heading size="xl" level="1">{{ __('hll.clans.rosters.list') }}</flux:heading>
                <flux:heading size="lg" level="2">{{ $clan->alias }} | {{ $clan->name }}</flux:heading>
                <flux:subheading size="base" class="mb-6">{{ $clan->description ?? __('hll.clans.show.no_description') }}</flux:subheading>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                {{ __('hll.clans.rosters.back_to_clan') }}
            </flux:button>

            @can('update', $clan)
                <flux:button variant="primary" color="blue" size="sm" href="{{ route('rosters.create', $clan->slug) }}">
                    {{ __('hll.clans.rosters.create_button') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <flux:separator variant="subtle" />

    @include('partials.flash')

    <div class="mt-4">
        @if ($this->rosters->isEmpty())
            <div class="py-12">
                <div class="text-center">
                    <flux:heading size="lg">{{ __('hll.clans.rosters.no_rosters') }}</flux:heading>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @foreach ($this->rosters as $roster)
                    <flux:card wire:key="roster-{{ $roster->id }}" class="p-2.5">
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <div class="flex-1">
                                    <div class="text-xl">{{ $roster->name }}</div>
                                    <div class="text-xs italic mx-4">{{ $roster->description ?? __('hll.clans.rosters.no_roster_description') }}</div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <flux:button variant="ghost" size="xs" href="{{ route('rosters.template', [$clan->slug, $roster->uuid]) }}">
                                        {{ __('hll.clans.rosters.manage_roster') }}
                                    </flux:button>
                                    @can('update', $roster)
                                        <flux:button variant="primary" color="green" size="xs" href="{{ route('rosters.edit', [$clan->slug, $roster->uuid]) }}">
                                            {{ __('hll.clans.rosters.edit_button') }}
                                        </flux:button>
                                    @endcan
                                    @can('delete', $roster)
                                        <flux:button variant="primary" color="red" size="xs" wire:click="showDeleteRoster({{ $roster->id }})">
                                            {{ __('hll.clans.rosters.delete_button') }}
                                        </flux:button>
                                    @endcan
                                </div>
                            </div>
                            <flux:separator variant="subtle" />
                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>{{ __('hll.clans.rosters.table.map') }}</flux:table.column>
                                    <flux:table.column>{{ __('hll.clans.rosters.table.central_point') }}</flux:table.column>
                                    <flux:table.column>{{ __('hll.clans.rosters.table.faction') }}</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    <flux:table.row>
                                        <flux:table.cell>{{ $roster->map?->name ?? '--' }}</flux:table.cell>
                                        <flux:table.cell>{{ $roster->centralPoint?->name ?? '--' }}</flux:table.cell>
                                        <flux:table.cell>{{ $roster->faction?->label() ?? '--' }}</flux:table.cell>
                                    </flux:table.row>
                                </flux:table.rows>
                            </flux:table>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>

    <flux:modal name="delete-roster-manager" class="max-w-lg" :closable="false">
        <div class="space-y-4">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.rosters.delete.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.rosters.delete.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>

            <flux:text>{{ __('hll.clans.rosters.delete.confirmation_message') }}</flux:text>

            <flux:text color="yellow">{{ __('hll.clans.rosters.delete.current_name_write', ['name' => $currentNameToDelete]) }}</flux:text>

            <flux:input size="sm" label="{{ __('hll.clans.rosters.delete.current_name') }}" wire:model="current_name" placeholder="{{ $currentNameToDelete }}" />

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancelDeleteRoster">{{ __('hll.commons.cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="red" wire:click="deleteRoster">{{ __('hll.clans.rosters.delete.button') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
