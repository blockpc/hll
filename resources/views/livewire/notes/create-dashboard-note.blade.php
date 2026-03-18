<div>
    <flux:modal.trigger name="create-note">
        <flux:button variant="primary" size="sm">{{ __('New Note') }}</flux:button>
    </flux:modal.trigger>

    <flux:modal name="create-note" class="max-w-lg" :closable="false">
        <div class="space-y-3">
            <h2 class="text-lg">{{ __('New Note') }}</h2>
            <div>
                <flux:input label="{{ __('Title') }}" wire:model="title" />
            </div>

            <div>
                <flux:textarea label="{{ __('Content') }}" wire:model="content" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="blue" wire:click="create">{{ __('Create Note') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
