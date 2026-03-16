<div class="w-full">
    <div class="relative mb-6 w-full">
        <div class="flex items-start justify-between space-x-6">
            <div class="flex space-x-6">
                <div>
                    @if ($clan->logo)
                        <img src="{{ $clan->logo_url }}" alt="{{ $clan->name }}" class="h-16 w-16 rounded-full object-cover">
                    @else
                        <x-placeholder-pattern class="h-16 w-16 rounded-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    @endif
                </div>
                <div>
                    <flux:heading size="xl" level="1">{{ __('hll.clans.managers.list') }}</flux:heading>
                    <flux:heading size="lg" level="2">{{ $clan->alias }} | {{ $clan->name }}</flux:heading>
                    <flux:subheading size="base" class="mb-6">{{ $clan->description ?? __('hll.clans.show.no_description') }}</flux:subheading>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @can('update', $clan)
                    <flux:button variant="outline" color="blue" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                        {{ __('hll.clans.managers.back') }}
                    </flux:button>

                    <flux:modal.trigger name="create-helper-manager">
                        <flux:button variant="primary" color="blue" size="sm" class="w-full">{{ __('hll.clans.managers.create.title') }}</flux:button>
                    </flux:modal.trigger>
                @endcan
            </div>
        </div>
        <flux:separator variant="subtle" />

        <flux:card class="p-2.5 space-y-6">
            <div class="flex justify-between items-start">
                <div>
                    <flux:heading size="lg">{{ __('hll.clans.show.titles.helpers') }}</flux:heading>
                    <flux:text class="mt-2">{{ trans_choice('hll.clans.show.titles.helpers_count', $this->members->count()) }}</flux:text>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse ($this->members as $member)
                        <flux:card class="p-2.5">
                            <div class="space-y-4">
                                <flux:heading size="base">{{ $member->name }}</flux:heading>
                                <flux:text class="text-xs italic">{{ $member->pivot->membership_role->label() }}</flux:text>

                                @can('update', $clan)
                                    <div class="flex justify-start items-center space-x-2">
                                        <flux:button size="xs" variant="primary" color="green" icon="pencil" wire:click="showEditModal({{ $member->id }})">{{ __('hll.commons.edit') }}</flux:button>

                                        <flux:button size="xs" variant="primary" color="red" icon="trash" wire:click="showDeleteModal({{ $member->id }})">{{ __('hll.commons.delete') }}</flux:button>
                                    </div>
                                @endcan
                            </div>
                        </flux:card>
                    @empty
                        <flux:text class="text-center text-gray-500">{{ __('hll.clans.managers.no_helpers') }}</flux:text>
                    @endforelse
                </div>
            </div>
        </flux:card>
    </div>

    <flux:modal name="create-helper-manager" class="w-1/2" :closable="false">
        <div class="space-y-3">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.managers.create.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.managers.create.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>
            <div>
                <flux:input label="{{ __('hll.clans.managers.form.name') }}" wire:model="name" />
            </div>
            <div>
                <flux:input type="email" label="{{ __('hll.clans.managers.form.email') }}" wire:model="email" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="blue" wire:click="save">{{ __('hll.clans.managers.create.title') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="edit-helper-manager" class="w-1/2" :closable="false">
        <div class="space-y-3">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.managers.edit.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.managers.edit.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>
            <div>
                <flux:input label="{{ __('hll.clans.managers.form.name') }}" wire:model="editingHelperName" />
            </div>
            <div>
                <flux:input type="email" label="{{ __('hll.clans.managers.form.email') }}" wire:model="editingHelperEmail" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancelModalHelper">{{ __('hll.commons.cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="green" wire:click="editHelper">{{ __('hll.clans.managers.edit.button') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-helper-manager" class="w-1/2" :closable="false">
        <div class="space-y-3">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.managers.delete.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.managers.delete.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>

            <flux:text>{{ __('hll.clans.managers.delete.confirmation_message') }}</flux:text>

            <flux:text color="yellow">{{ __('hll.clans.managers.delete.current_name_write', ['name' => $editingHelperName]) }}</flux:text>

            <flux:input label="{{ __('hll.clans.managers.delete.current_name') }}" wire:model="current_name" aria-placeholder="{{ $editingHelperName }}" placeholder="{{ $editingHelperName }}" />
            @error('editingHelperName')
                <flux:text class="text-red-500 text-sm mt-1">{{ $message }}</flux:text>
            @enderror

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancelDeleteModal">{{ __('hll.commons.cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="red" wire:click="delete">{{ __('hll.clans.managers.delete.button') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
