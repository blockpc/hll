<div>
    <div class="relative mb-6 w-full">
        <div class="flex items-start justify-between space-x-6">
            <x-header-clan :clan="$clan" :title="__('hll.clans.soldiers.list')" />
            <div class="flex flex-col h-24">
                <div class="flex items-center space-x-2">
                    @can('update', $clan)
                        <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                            {{ __('hll.clans.soldiers.back') }}
                        </flux:button>

                        <div>
                            <flux:modal.trigger name="create-soldier-manager">
                                <flux:button variant="primary" color="blue" size="sm" class="w-full">{{ __('hll.clans.soldiers.create.title') }}</flux:button>
                            </flux:modal.trigger>
                        </div>
                    @endcan
                </div>

                <div class="flex items-center justify-end space-x-2 mt-auto pb-1">
                    <!-- Export Button -->
                    @if (!$importFile && $this->soldiers->isNotEmpty())
                    <flux:button variant="primary" size="sm" class="w-full p-2" icon="arrow-down-tray" wire:click="exportSoldiers" tooltip="{{ __('hll.clans.soldiers.export.subtitle') }}" />
                    @endif

                    <!-- Import Button -->
                    <div x-data="{ uploading: false, progress: 0 }"
                        x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-cancel="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                        class="flex items-center space-x-2">

                        <input type="file" wire:model="importFile" id="import-file" x-ref="importFile" class="hidden" />

                        @if (!$importFile)
                        <flux:button x-on:click="$refs.importFile.click()" icon="arrow-up-tray" size="sm" tooltip="{{ __('hll.clans.soldiers.import.select_file') }}" />

                        <flux:button wire:click="downloadImportTemplate" variant="primary" color="yellow" icon="arrow-down-tray" size="sm" tooltip="{{ __('hll.clans.soldiers.download-template') }}" />
                        @endif

                        @if ($importFile)
                        <flux:button wire:click="import" variant="primary" icon="arrow-up-tray" x-bind:disabled="uploading" tooltip="{{ __('hll.clans.soldiers.import.start_import') }}" size="sm" />

                        <flux:button wire:click="resetImportFile" variant="primary" color="red" icon="x-mark" tooltip="{{ __('hll.clans.soldiers.import.cancel_import') }}" size="sm" />
                        @endif
                    </div>
                </div>
                <flux:error name="importFile" />
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

            <flux:table :paginate="$this->soldiers">
                <flux:table.columns>
                    <flux:table.column>Avatar</flux:table.column>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Role</flux:table.column>
                    <flux:table.column># Rosters</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @if ($this->soldiers->isEmpty())
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-4">
                                {{ __('hll.clans.soldiers.no_soldiers') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @else
                        @foreach ($this->soldiers as $soldier)
                            <flux:table.row wire:key="soldier-{{ $soldier->id }}">
                                <flux:table.cell>
                                    <flux:avatar :name="$soldier->name" />
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-base">{{ $soldier->name }}</span>
                                    <p class="text-xs">{{ $soldier->observation ?? __('hll.clans.soldiers.no_observation') }}</p>
                                </flux:table.cell>
                                <flux:table.cell>{{ $soldier->role?->label() ?? __('hll.clans.soldiers.no_role') }}</flux:table.cell>
                                <flux:table.cell>{{ $soldier->squads->count() ?? 0 }}</flux:table.cell>
                                <flux:table.cell align="end">
                                    @can('update', $clan)
                                        <div class="">
                                            <flux:button size="xs" variant="primary" color="green" icon="pencil" wire:click="showEditSoldier({{ $soldier->id }})">
                                                {{ __('hll.commons.edit') }}
                                            </flux:button>
                                            <flux:button size="xs" variant="primary" color="red" icon="trash" wire:click="showDeleteSoldier({{ $soldier->id }})">
                                                {{ __('hll.commons.delete') }}
                                            </flux:button>
                                        </div>
                                    @endcan
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    @endif
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>

    <flux:modal name="create-soldier-manager" class="max-w-lg" :closable="false">
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
                <flux:button size="sm" variant="ghost" wire:click="cancel">{{ __('hll.commons.cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="blue" wire:click="save">{{ __('hll.clans.soldiers.create.button') }}</flux:button>
            </div>

        </div>
    </flux:modal>

    <flux:modal name="edit-soldier-manager" class="max-w-lg" :closable="false">
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
                <flux:button size="sm" variant="ghost" wire:click="cancelEditSoldier">{{ __('hll.commons.cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="green" wire:click="editSoldier">{{ __('hll.clans.soldiers.edit.button') }}</flux:button>
            </div>

        </div>
    </flux:modal>

    <flux:modal name="delete-soldier-manager" class="max-w-lg" :closable="false">
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
                <flux:button size="sm" variant="ghost" wire:click="cancelDeleteSoldier">{{ __('hll.commons.cancel') }}</flux:button>
                <flux:button size="sm" variant="primary" color="red" wire:click="deleteSoldier">{{ __('hll.clans.soldiers.delete.button') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
