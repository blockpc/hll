<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('hll.maps.index.submenu') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('hll.maps.index.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="">
                    <flux:input icon="magnifying-glass" :loading="false" :clearable="true" placeholder="{{ __('hll.maps.index.search_maps') }}" wire:model.live.debounce.500ms="search" class="max-w-md w-full" size="sm" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->maps as $map)
                    <flux:card>
                        <div class="flex flex-col justify-between h-full">
                            <div class="">
                                <flux:heading size="lg">{{ $map->name }}</flux:heading>
                                <flux:text size="sm" class="mt-2 mb-4">{{ \Illuminate\Support\Str::limit($map->description, 120) }}</flux:text>
                            </div>
                            <div class="flex gap-2">
                                {{-- <flux:spacer />
                                <flux:button size="xs" variant="subtle" wire:click="mapOpen({{ $map->id }})">{{ __('Read Map') }}</flux:button>
                                <flux:button size="xs" variant="primary" color="green" wire:click="mapEdit({{ $map->id }})">{{ __('Edit Map') }}</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="mapDelete({{ $map->id }})">{{ __('Delete Map') }}</flux:button> --}}
                            </div>
                        </div>
                    </flux:card>
                @empty
                    <p class="mt-4 p-4 col-span-full text-sm text-neutral-600 dark:text-neutral-400">{{ __('hll.maps.index.empty') }}</p>
                @endforelse
            </div>
            <div>
                <flux:pagination :paginator="$this->maps" />
            </div>
        </div>
</div>
