<div class="relative mb-6 w-full">
    @include('partials.rosters-header')

    <flux:separator variant="subtle" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="grid gap-4">
            <flux:input size="sm" label="{{ __('hll.clans.rosters.form.name') }}" placeholder="{{ __('hll.clans.rosters.form.name') }}" wire:model="name" />

            <flux:textarea label="{{ __('hll.clans.rosters.form.description') }}" placeholder="{{ __('hll.clans.rosters.form.description') }}" wire:model="description" rows="4" />

            <flux:input type="number" size="sm" min="0" label="{{ __('hll.clans.rosters.form.max_soldiers') }}" placeholder="{{ __('hll.clans.rosters.form.max_soldiers') }}" wire:model="max_soldiers" />

            <div class="grid grid-cols-3 gap-4">
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
            </div>

            <flux:separator variant="subtle" />

            <flux:checkbox label="{{ __('hll.clans.rosters.form.is_public') }}" wire:model="is_public" />
            {{-- <flux:checkbox label="{{ __('hll.clans.rosters.form.is_multiclan') }}" wire:model="is_multiclan" disabled /> --}}
            {{-- <flux:checkbox label="{{ __('hll.clans.rosters.form.is_multifaction') }}" wire:model="is_multifaction" disabled /> --}}

            <flux:separator variant="subtle" />

            <div class="flex justify-end space-x-2">
                <flux:button variant="ghost" href="{{ route('rosters.table', $clan->slug) }}">
                    {{ __('hll.clans.rosters.back_to_rosters') }}
                </flux:button>
                <flux:button variant="primary" color="green" size="sm" wire:click="save" wire:loading.attr="disabled">
                    {{ __('hll.clans.rosters.edit.save_button') }}
                </flux:button>
            </div>
        </div>
        <div class="grid gap-4">
            <x-input-file-single name="image" title="{{ __('hll.clans.rosters.form.image') }}" wire:model.live="image" stacked>
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 h-16 w-16">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" alt="{{ __('hll.clans.rosters.form.image') }}" class="h-full w-full object-cover">
                    @elseif ($roster->image)
                        <img src="{{ $roster->image_url }}" alt="{{ __('hll.clans.rosters.form.image') }}" class="h-full w-full object-cover">
                    @else
                        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    @endif
                </div>
            </x-input-file-single>
        </div>
    </div>
</div>
