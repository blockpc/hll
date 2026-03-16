@props(['name', 'title' => __('components.input_file.default_title'), 'stacked' => false])

<div @class([
    'flex',
    'items-center justify-start space-x-4' => ! $stacked,
    'flex-col items-center space-y-4' => $stacked,
])>

    {{ $slot }}

    <div @class([
        'flex flex-col',
        'items-start' => ! $stacked,
        'items-center' => $stacked,
    ])
        x-data="{ isUploading: false, progress: 0 }"
        x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false; progress = 0"
        x-on:livewire-upload-error="isUploading = false; progress = 0"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
    >
        <div wire:loading.remove wire:target="{{ $attributes->wire('model')->value() }}">
            <input type='file' class="peer sr-only" id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class') }} />
            <label @class([
                'flex items-center space-x-2 text-sm cursor-pointer border border-gray-400 p-2 rounded',
                'peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-blue-500',
                'flex-1' => ! $stacked,
            ]) for="{{ $name }}">
                <flux:icon icon="arrow-up-tray" class="w-4" />
                <div @class([
                    'hidden md:block',
                    'text-center' => $stacked,
                ])>{{ $title }}</div>
            </label>
        </div>
        <div class="btn btn-sm btn-default w-full" wire:loading.block wire:target="{{ $attributes->wire('model')->value() }}">{{ __('hll.commons.loading') }}</div>
        <div class="px-1 w-full mt-1" x-show="isUploading">
            <progress class="block h-2 w-full rounded accent-blue-500" max="100" x-bind:value="progress"></progress>
        </div>
        @isset($errors)
            @error($attributes->wire('model')->value())
                <p @class([
                    'text-red-400 text-sm',
                    'text-center' => $stacked,
                ])>{{ $message }}</p>
            @enderror
        @endisset
    </div>
</div>
