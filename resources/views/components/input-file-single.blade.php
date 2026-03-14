@props(['name', 'title' => 'Vincular Archivo(s)'])

<div class="flex items-center justify-start space-x-4">

    {{ $slot }}

    <div class="flex flex-col items-start"
        x-data="{ isUploading: false, progress: 0 }"
        x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false; progress = 0"
        x-on:livewire-upload-error="isUploading = false; progress = 0"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
    >
        <label class="flex flex-1 items-center space-x-2 text-sm cursor-pointer border border-gray-400 p-2 rounded" for="{{ $name }}" wire:loading.remove wire:target="{{ $attributes->wire('model')->value() }}">
            <flux:icon icon="arrow-up-tray" class="w-4" />
            <div class="hidden md:block">{{ $title }}</div>
            <input type='file' class="hidden" id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class') }} />
        </label>
        <div class="btn-sm btn-default w-full" wire:loading wire:target="{{ $attributes->wire('model')->value() }}">{{ __('loading') }}</div>
        <div class="btn-sm btn-default w-full" wire:loading wire:target="{{ $attributes->wire('model')->value() }}">{{ __('loading') }}</div>
        <div class="px-1" x-show="isUploading">
            <progress class="w-full h-2" max="100" x-bind:value="progress"></progress>
        </div>
        @error($attributes->wire('model')->value())
            <p class="text-red-400 text-sm">{{ $message }}</p>
        @enderror
    </div>
</div>
