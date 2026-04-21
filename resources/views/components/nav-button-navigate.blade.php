@props(['route', 'routeParams' => []])

@if (request()->url() === route($route, $routeParams))
    <flux:button {{ $attributes->merge(['size' => 'xs', 'variant' => 'ghost']) }}>
        {{ $slot }}
    </flux:button>
@else
    <flux:button {{ $attributes->merge(['size' => 'xs', 'variant' => 'primary']) }} :href="route($route, $routeParams)" wire:navigate>
        {{ $slot }}
    </flux:button>
@endif
