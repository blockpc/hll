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
                <flux:heading size="xl" level="1">{{ __('hll.clans.rosters.create.title') }}</flux:heading>
                <flux:heading size="lg" level="2">{{ $clan->alias }} | {{ $clan->name }}</flux:heading>
                <flux:subheading size="base" class="mb-6">{{ $clan->description ?? __('hll.clans.show.no_description') }}</flux:subheading>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @can('update', $clan)
                <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                    {{ __('hll.clans.rosters.back_to_clan') }}
                </flux:button>

                <flux:button variant="ghost" size="sm" href="{{ route('rosters.table', $clan->slug) }}">
                    {{ __('hll.clans.rosters.back_to_rosters') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="grid gap-4">
            <flux:input size="sm" label="{{ __('hll.clans.rosters.form.name') }}" placeholder="{{ __('hll.clans.rosters.form.name') }}" wire:model="name" />

            <flux:textarea label="{{ __('hll.clans.rosters.form.description') }}" placeholder="{{ __('hll.clans.rosters.form.description') }}" wire:model="description" rows="4" />

            <flux:input size="sm" label="{{ __('hll.clans.rosters.form.max_soldiers') }}" placeholder="{{ __('hll.clans.rosters.form.max_soldiers') }}" wire:model="max_soldiers" type="number" />

            <flux:select label="{{ __('hll.clans.rosters.form.map_id') }}" wire:model.live="map_id" placeholder="{{ __('hll.clans.rosters.form.map_id') }}">
                <flux:select.option value="">{{ __('hll.commons.select') }}</flux:select.option>
                @foreach ($this->maps as $mapId => $mapName)
                    <flux:select.option value="{{ $mapId }}">{{ $mapName }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select label="{{ __('hll.clans.rosters.form.central_point_id') }}" wire:model.live="central_point_id" placeholder="{{ __('hll.clans.rosters.form.central_point_id') }}">
                <flux:select.option value="">{{ __('hll.commons.select') }}</flux:select.option>
                @foreach ($this->centralPoints as $centralPointId => $centralPointName)
                    <flux:select.option value="{{ $centralPointId }}">{{ $centralPointName }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select label="{{ __('hll.clans.rosters.form.faction') }}" wire:model.live="faction" placeholder="{{ __('hll.clans.rosters.form.faction') }}">
                <flux:select.option value="">{{ __('hll.commons.select') }}</flux:select.option>
                @foreach ($this->factions as $factionType)
                    <flux:select.option value="{{ $factionType->value }}">{{ $factionType->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:separator variant="subtle" />

            <div class="flex justify-end space-x-2">
                <flux:button variant="ghost" size="sm" href="{{ route('rosters.table', $clan->slug) }}">
                    {{ __('hll.clans.rosters.back_to_rosters') }}
                </flux:button>
                <flux:button variant="primary" color="blue" size="sm" wire:click="save" wire:loading.attr="disabled">
                    {{ __('hll.clans.rosters.create.save_button') }}
                </flux:button>
            </div>
        </div>
        <div class="grid gap-4">
            <x-input-file-single name="image" title="{{ __('hll.clans.rosters.form.image') }}" wire:model.live="image" stacked>
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 h-16 w-16">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" alt="{{ __('hll.clans.rosters.form.image') }}" class="h-full w-full object-cover">
                    @else
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    @endif
                </div>
            </x-input-file-single>
        </div>
    </div>
</div>
