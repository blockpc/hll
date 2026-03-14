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

    <div class="flex flex-col space-y-2">
        <flux:text class="text-zinc-500">{{ __('loading') }}</flux:text>
    </div>
</div>
