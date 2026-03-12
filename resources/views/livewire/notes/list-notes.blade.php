<div>
    <div class="w-full space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">{{ __('Notes') }}</h1>

            {{-- Botón: abrir modal crear (Flux) --}}
            <flux:button variant="primary" color="blue" wire:click="$set('createOpen', true)" size="sm">{{ __('New Note') }}</flux:button>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="">
                    <flux:input icon="magnifying-glass" :loading="false" :clearable="true" placeholder="{{ __('Search notes...') }}" wire:model.live.debounce.500ms="search" class="max-w-md w-full" size="sm" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->notes as $note)
                    <flux:card>
                        <div class="flex flex-col justify-between h-full">
                            <div class="">
                                <flux:heading size="lg">{{ $note->title }}</flux:heading>
                                <flux:text class="mt-2 mb-4">{{ \Illuminate\Support\Str::limit($note->content, 120) }}</flux:text>
                            </div>
                            <div class="flex gap-2">
                                <flux:spacer />
                                <flux:button size="xs" variant="subtle" wire:click="openNote({{ $note->id }})">{{ __('Read Note') }}</flux:button>
                                <flux:button size="xs" variant="primary" color="green" wire:click="openEdit({{ $note->id }})">{{ __('Edit Note') }}</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="openDelete({{ $note->id }})">{{ __('Delete Note') }}</flux:button>
                            </div>
                        </div>
                    </flux:card>
                @empty
                    <p class="mt-4 p-4 col-span-full text-sm text-neutral-600 dark:text-neutral-400">{{ __('No notes found.') }}</p>
                @endforelse
            </div>
            <div>
                <flux:pagination :paginator="$this->notes" />
            </div>
        </div>
    </div>
    {{-- modal crear --}}
    <flux:modal name="create-note" wire:model="createOpen" class="w-1/2" :closable="false">
        <div class="space-y-3">
            <h2 class="text-lg">{{ __('New Note') }}</h2>
            <div>
                <flux:input label="{{ __('Title') }}" wire:model="title" />
            </div>

            <div>
                <flux:textarea label="{{ __('Content') }}" wire:model="content" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" color="yellow" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="blue" wire:click="create">{{ __('Create Note') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- modal editar --}}
    <flux:modal name="edit-note" wire:model="editOpen" class="w-1/2" :closable="false">
        <div class="space-y-3">
            <h2 class="text-lg">{{ __('Edit Note') }}</h2>
            <div>
                <flux:input label="{{ __('Title') }}" wire:model="title" />
            </div>

            <div>
                <flux:textarea label="{{ __('Content') }}" wire:model="content" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" color="yellow" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="green" wire:click="update">{{ __('Save changes') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- modal eliminar --}}
    <flux:modal name="delete-note" wire:model="deleteOpen" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <h2 class="text-lg">{{ __('Delete Note') }}</h2>
            <p>{{ __('Are you sure you want to delete this note? This action cannot be undone.') }}</p>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" color="yellow" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="danger" wire:click="destroy">{{ __('Delete Note') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- modal ver nota completa --}}
    <flux:modal name="view-note" wire:model="viewOpen" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <h2 class="text-lg">{{ $title }}</h2>
            <p class="text-sm">{{ $content }}</p>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" wire:click="cancel">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
