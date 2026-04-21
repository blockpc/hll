@props(['clan', 'title' => 'Clan'])

<div class="flex space-x-6 h-24">
    <div class="flex-1">
        @if ($clan?->logo)
            <img src="{{ $clan->logo_url }}" alt="{{ $clan->name }}" class="h-20 w-20 rounded-full object-cover">
        @else
            <x-placeholder-pattern class="h-20 w-20 rounded-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        @endif
    </div>
    <div class="flex flex-col justify-between">
        <div class="text-3xl font-bold">{{ __($title) }}</div>
        <div class="text-xl font-semibold">{{ $clan->alias }} | {{ $clan->name }}</div>
        <div class="text-sm italic">{{ $clan->description ?? __('hll.clans.show.no_description') }}</div>
    </div>
</div>
