@props([
    'sidebar' => false,
])

@php
    $name = config('app.name', 'Laravel Starter Kit');
    $logoSlot = 'flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground';
@endphp

@if($sidebar)
    <flux:sidebar.brand :$name {{ $attributes }}>
        <x-slot name="logo" class="{{ $logoSlot }}">
            <img src="{{ asset('images/logo-hll.png') }}" alt="{{ $name }}" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :$name {{ $attributes }}>
        <x-slot name="logo" class="{{ $logoSlot }}">
            <img src="{{ asset('images/logo-hll.png') }}" alt="{{ $name }}" />
        </x-slot>
    </flux:brand>
@endif
