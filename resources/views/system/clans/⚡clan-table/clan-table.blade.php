<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('hll.clans.index.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.index.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-col space-y-2">
        <div class="flex items-center justify-between">
            <flux:input icon="magnifying-glass" :loading="false" :clearable="true" placeholder="{{ __('hll.clans.index.search_clans') }}" wire:model.live.debounce.500ms="search" class="max-w-64" autocomplete="off" />
            @can('create', App\Models\Clan::class)
            <flux:button variant="primary" color="blue" size="sm" href="{{ route('clans.create') }}">{{ __('hll.clans.index.create') }}</flux:button>
            @endcan
        </div>

        <x-tables.table>
            <x-slot name="thead">
                <tr class="tr">
                    <th scope="col" class="td">{{ __('hll.clans.index.table.logo') }}</th>
                    <th scope="col" class="td">{{ __('hll.clans.index.table.alias_name') }}</th>
                    <th scope="col" class="td">{{ __('hll.clans.index.table.owner') }}</th>
                    <th scope="col" class="td" align="end">{{ __('hll.clans.index.table.actions') }}</th>
                </tr>
            </x-slot>
            <x-slot name="tbody">
                @forelse ($this->clanes as $clan)
                    <tr class="tr tr-hover">
                        <td class="td">
                            @if ($clan->logo)
                            <img src="{{ $clan->logoUrl }}" alt="{{ __('hll.clans.form_fields.logo') }}" class="h-12 w-12 object-cover rounded-full">
                            @else
                            <div class="relative overflow-hidden rounded-full border border-neutral-200 dark:border-neutral-700 w-12 h-12">
                                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                            </div>
                            @endif
                        </td>
                        <td class="td">
                            <div class="flex flex-col gap-1">
                                <span class="text-base">{{ $clan->alias }} | {{ $clan->name }}</span>
                                <span class="text-xs italic">{{ $clan->description }}</span>
                            </div>
                        </td>
                        <td class="td">
                            <span>{{ $clan->owner->name }}</span>
                        </td>
                        <td class="td flex justify-end space-x-2">
                            @can('clans.show')
                            <flux:button variant="primary" size="xs" href="{{ route('clans.show', $clan->slug) }}" icon="eye" />
                            @endcan
                            @can('update', $clan)
                            <flux:button variant="primary" color="green" size="xs" icon="pencil" href="{{ route('clans.edit', $clan->slug) }}" />
                            @endcan
                            @can('clans.delete')
                            <flux:button variant="danger" size="xs" wire:click="confirmDelete({{ $clan->id }})" icon="x-mark" />
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr class="tr">
                        <td class="td text-center" colspan="4">
                            {{ __('hll.clans.index.empty') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-tables.table>

        <flux:pagination :paginator="$this->clanes" />
    </div>
</div>
