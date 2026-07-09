@props([
    'squad',
])

<flux:button variant="outline" size="xs" class="rounded border text-amber-300 border-amber-500/30! bg-amber-500/30! uppercase text-[10px] font-bold">
    {{ $squad->alias }}
</flux:button>
