<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="p-4! lg:p-6!">
        {{ $slot }}
    </flux:main>
    <livewire:notifications.user-panel />
    <livewire:alert />
</x-layouts::app.sidebar>
