<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('system.permissions.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('system.permissions.description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-col space-y-2">
        @include('partials.flash')

        <div class="flex space-x-2">
            <flux:input icon="magnifying-glass" :loading="false" :clearable="true" placeholder="{{ __('system.permissions.search-permissions') }}" wire:model.live.debounce.500ms="search" class="max-w-64" autocomplete="off" />

            <flux:select wire:model.live="key" class="w-48">
                <flux:select.option value="">{{ __('Choose key...') }}</flux:select.option>
                @foreach ($this->keywords as $keyValue => $keyName)
                    <flux:select.option value="{{ $keyValue }}">{{ __('system.permissions.keys.'.$keyName) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <x-tables.table>
            <x-slot name="thead">
                <tr class="tr">
                    <th scope="col" class="td">{{ __('system.permissions.table.name') }}</th>
                    <th scope="col" class="td">{{ __('system.permissions.table.description') }}</th>
                    <th scope="col" class="td">{{ __('system.permissions.table.key') }}</th>
                    <th scope="col" class="td" align="end">{{ __('system.permissions.table.actions') }}</th>
                </tr>
            </x-slot>
            <x-slot name="tbody">
                @forelse ($this->permissions as $permission)
                    <tr class="tr tr-hover">
                        <td class="td">
                            <div class="flex flex-col gap-1">
                                <span class="text-base">{{ $permission->display_name }}</span>
                                <span class="text-xs italic">{{ $permission->name }}</span>
                            </div>
                        </td>
                        <td class="td">
                            <span class="text-xs italic">{{ $permission->description }}</span>
                        </td>
                        <td class="td">
                            <span class="text-sm">{{ __('system.permissions.keys.'.$permission->key) }}</span>
                        </td>
                        <td class="td text-right">
                            @can('permissions.edit')
                            <flux:button variant="primary" color="green" size="sm" wire:click="editPermission({{ $permission->id }})">{{ __('system.permissions.table.edit') }}</flux:button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr class="tr">
                        <td class="td text-center" colspan="4">
                            {{ __('system.permissions.table.no_permissions') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-tables.table>

        <flux:pagination :paginator="$this->permissions" />
    </div>

    {{-- modal editar --}}
    <flux:modal name="edit-permission" wire:model="showEditModal" class="w-1/2" :closable="false">
        <div class="space-y-3">
            <h2 class="text-lg">{{ __('system.permissions.edit.title') }}</h2>
            <div>
                <flux:input label="{{ __('system.permissions.edit.display_name') }}" wire:model="display_name" />
            </div>

            <div>
                <flux:textarea label="{{ __('system.permissions.edit.description') }}" wire:model="description" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" color="yellow" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                @can('permissions.edit')
                <flux:button size="sm" variant="primary" color="green" wire:click="savePermission">{{ __('system.permissions.edit.save') }}</flux:button>
                @endcan
            </div>
        </div>
    </flux:modal>
</div>
