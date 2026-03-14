<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ $this->clan->alias }} | {{ $this->clan->name }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ $this->clan->description ?? __('hll.clans.show.no_description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-col space-y-2">
        {{ __('loading') }}
    </div>
</div>
