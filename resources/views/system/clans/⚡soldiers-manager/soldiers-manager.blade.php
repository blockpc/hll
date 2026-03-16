<div>
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
                    <flux:heading size="xl" level="1">{{ __('hll.clans.soldiers.list') }}</flux:heading>
                    <flux:heading size="lg" level="2">{{ $clan->alias }} | {{ $clan->name }}</flux:heading>
                    <flux:subheading size="base" class="mb-6">{{ $clan->description ?? __('hll.clans.show.no_description') }}</flux:subheading>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @can('update', $clan)
                    <flux:button variant="outline" color="blue" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                        {{ __('hll.clans.soldiers.back') }}
                    </flux:button>

                    <flux:modal.trigger name="create-soldier-manager">
                        <flux:button variant="primary" color="blue" size="sm" class="w-full">{{ __('hll.clans.soldiers.create.title') }}</flux:button>
                    </flux:modal.trigger>
                @endcan
            </div>
        </div>

        <flux:separator variant="subtle" />

        <flux:card class="p-2.5 space-y-6 mt-4">
            <div class="flex justify-between items-start">
                <div>
                    <flux:heading size="lg">{{ __('hll.clans.soldiers.list') }}</flux:heading>
                    <flux:text class="mt-2">{{ trans_choice('hll.clans.soldiers.list_count', $this->soldiers->total()) }}</flux:text>
                </div>
            </div>

            @if ($this->soldiers->isEmpty())
                <flux:text class="text-center text-gray-500">{{ __('hll.clans.soldiers.no_soldiers') }}</flux:text>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($this->soldiers as $soldier)
                        <flux:card wire:key="soldier-{{ $soldier->id }}" class="p-2.5 space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:heading size="base">{{ $soldier->name }}</flux:heading>
                                <flux:text class="text-xs italic">{{ $soldier->role?->label() ?? __('hll.clans.soldiers.no_role') }}</flux:text>
                            </div>
                            <flux:text class="text-xs italic">{{ $soldier->observation ?? __('hll.clans.soldiers.no_observation') }}</flux:text>
                            @can('update', $clan)
                            <div>
                                <flux:button size="xs" variant="primary" color="green" icon="pencil" wire:click="showEditSoldier({{ $soldier->id }})">
                                    {{ __('hll.commons.edit') }}
                                </flux:button>
                                <flux:button size="xs" variant="primary" color="red" icon="trash" wire:click="showDeleteSoldier({{ $soldier->id }})">
                                    {{ __('hll.commons.delete') }}
                                </flux:button>
                            </div>
                            @endcan
                        </flux:card>
                    @endforeach
                </div>
            @endif

            <flux:pagination :paginator="$this->soldiers" />
        </flux:card>
    </div>

    <flux:modal name="create-soldier-manager" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.soldiers.create.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.soldiers.create.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>

            <div>
                <flux:text color="yellow">{{ __('hll.clans.soldiers.create.message_about_names') }}</flux:text>
            </div>

            <div>
                <x-toggle name="many-soldiers-toggle" yes="{{ __('hll.clans.soldiers.create.yes_toggle') }}" not="{{ __('hll.clans.soldiers.create.not_toggle') }}" wire:model.live="manySoldiers" />
            </div>

            @if ($manySoldiers)
                <div>
                    <flux:textarea label="{{ __('hll.clans.soldiers.create.yes_toggle') }}" placeholder="{{ __('hll.clans.soldiers.create.yes_toggle_placeholder') }}" wire:model="bulkNames" rows="8" />
                </div>
            @else
                <div>
                    <flux:input size="sm" label="{{ __('hll.clans.soldiers.form.name') }}" wire:model="name" />
                </div>
                <div>
                    <flux:select size="sm" label="{{ __('hll.clans.soldiers.form.role') }}" wire:model="role">
                        <option value="">{{ __('hll.clans.soldiers.no_role') }}</option>
                        @foreach ($this->roleSquads as $roleSquadCreate)
                            <option value="{{ $roleSquadCreate->value }}">{{ $roleSquadCreate->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:input size="sm" label="{{ __('hll.clans.soldiers.form.observation') }}" wire:model="observation" />
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="blue" wire:click="save">{{ __('hll.clans.soldiers.create.button') }}</flux:button>
            </div>

        </div>
    </flux:modal>

    <flux:modal name="edit-soldier-manager" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.soldiers.edit.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.soldiers.edit.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>

            <div>
                <flux:text color="yellow">{{ __('hll.clans.soldiers.create.message_about_names') }}</flux:text>
            </div>

            <div>
                <flux:input size="sm" label="{{ __('hll.clans.soldiers.form.name') }}" wire:model="soldier_name" />
            </div>
            <div>
                <flux:select size="sm" label="{{ __('hll.clans.soldiers.form.role') }}" wire:model="soldier_role">
                    <option value="">{{ __('hll.clans.soldiers.no_role') }}</option>
                    @foreach ($this->roleSquads as $roleSquadEdit)
                        <option value="{{ $roleSquadEdit->value }}">{{ $roleSquadEdit->label() }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:input size="sm" label="{{ __('hll.clans.soldiers.form.observation') }}" wire:model="soldier_observation" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancelEditSoldier">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="green" wire:click="editSoldier">{{ __('hll.clans.soldiers.edit.button') }}</flux:button>
            </div>

        </div>
    </flux:modal>

    <flux:modal name="delete-soldier-manager" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">{{ __('hll.clans.soldiers.delete.title') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.soldiers.delete.subtitle') }}</flux:subheading>
                <flux:separator variant="subtle" />
            </div>

            <flux:text>{{ __('hll.clans.soldiers.delete.confirmation_message') }}</flux:text>

            <flux:text color="yellow">{{ __('hll.clans.soldiers.delete.current_name_write', ['name' => $currentNameToDelete]) }}</flux:text>

            <flux:input size="sm" label="{{ __('hll.clans.soldiers.delete.current_name') }}" wire:model="current_name" placeholder="{{ $currentNameToDelete }}" />

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="ghost" wire:click="cancelDeleteSoldier">{{ __('Cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="red" wire:click="deleteSoldier">{{ __('hll.clans.soldiers.delete.button') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
