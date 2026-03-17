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
            <flux:button variant="outline" color="blue" size="sm" href="{{ route('clans.show', $clan->slug) }}">
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
            <div class="flex flex-col space-y-4">
                @foreach ($this->rosters as $roster)
                    <flux:card wire:key="roster-{{ $roster->id }}" class="p-2.5">
                        <flux:heading size="md">{{ $roster->name }}</flux:heading>
                        <flux:subheading size="sm">
                            {{ $roster->description ?? __('hll.clans.rosters.no_roster_description') }}
                        </flux:subheading>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>
</div>
