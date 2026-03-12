@props([
    'name',
    'yes' => 'Si',
    'not' => 'No',
    'color' => 'blue',
    'default' => 'gray',
    'disabled' => false,
    'label' => null,
])

@php
    $colorVariants = [
        'blue' => [
            'ring' => 'peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800',
            'checkedBg' => 'peer-checked:bg-blue-600',
            'thumbBorder' => 'after:border-blue-300 dark:border-blue-600 peer-checked:after:border-blue-600',
        ],
        'green' => [
            'ring' => 'peer-focus:ring-green-300 dark:peer-focus:ring-green-800',
            'checkedBg' => 'peer-checked:bg-green-600',
            'thumbBorder' => 'after:border-green-300 dark:border-green-600 peer-checked:after:border-green-600',
        ],
        'red' => [
            'ring' => 'peer-focus:ring-red-300 dark:peer-focus:ring-red-800',
            'checkedBg' => 'peer-checked:bg-red-600',
            'thumbBorder' => 'after:border-red-300 dark:border-red-600 peer-checked:after:border-red-600',
        ],
        'yellow' => [
            'ring' => 'peer-focus:ring-yellow-300 dark:peer-focus:ring-yellow-800',
            'checkedBg' => 'peer-checked:bg-yellow-600',
            'thumbBorder' => 'after:border-yellow-300 dark:border-yellow-600 peer-checked:after:border-yellow-600',
        ],
        'zinc' => [
            'ring' => 'peer-focus:ring-zinc-300 dark:peer-focus:ring-zinc-700',
            'checkedBg' => 'peer-checked:bg-zinc-600',
            'thumbBorder' => 'after:border-zinc-300 dark:border-zinc-600 peer-checked:after:border-zinc-600',
        ],
    ];

    $defaultVariants = [
        'gray' => 'bg-gray-600',
        'zinc' => 'bg-zinc-600',
        'slate' => 'bg-slate-600',
        'neutral' => 'bg-neutral-600',
    ];

    $selectedColor = $colorVariants[$color] ?? $colorVariants['blue'];
    $selectedDefault = $defaultVariants[$default] ?? $defaultVariants['gray'];
@endphp

<div
    {{ $attributes->except(['wire:model.live'])->merge(['class' => 'flex text-xs font-semibold']) }}
    x-data="{ toggle: @entangle($attributes->wire('model')->value()).live }"
>
    <label
        for="{{ $name }}"
        @class([
            'inline-flex relative items-center',
            'cursor-pointer' => ! $disabled,
            'cursor-not-allowed opacity-60' => $disabled,
        ])
    >
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            class="sr-only peer"
            {{ $attributes->except('class') }}
            @disabled($disabled)
        />

        <div
            class="w-10 h-5 rounded-full peer-focus:outline-none peer-focus:ring-2
                   peer-checked:after:translate-x-full
                   after:content-[''] after:absolute after:top after:left-0
                   after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                   {{ $selectedDefault }} {{ $selectedColor['ring'] }} {{ $selectedColor['thumbBorder'] }} {{ $selectedColor['checkedBg'] }}"
        ></div>

        @if (! is_null($label))
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap">
                {{ __($label) }}
            </span>
        @else
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap" x-show="toggle" x-cloak>
                {{ __($yes) }}
            </span>

            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap" x-show="! toggle" x-cloak>
                {{ __($not) }}
            </span>
        @endif
    </label>
</div>
