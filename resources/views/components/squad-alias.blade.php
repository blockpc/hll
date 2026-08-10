@props([
    'squad',
])

<flux:badge rounded size="sm" color="orange" class="flex-col">
    <div class="flex items-center">
        <span class="text-white uppercase">{{ $squad->alias }}</span>
    </div>
</flux:badge>
