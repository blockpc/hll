<div>
    @if (session()->has('success'))
        <flux:callout variant="success" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger" class="mb-4">
            {{ session('error') }}
        </flux:callout>
    @endif

    @if (session()->has('warning'))
        <flux:callout variant="warning" class="mb-4">
            {{ session('warning') }}
        </flux:callout>
    @endif

    @if (session()->has('info'))
        <flux:callout variant="secondary" color="blue" class="mb-4">
            {{ session('info') }}
        </flux:callout>
    @endif
</div>
