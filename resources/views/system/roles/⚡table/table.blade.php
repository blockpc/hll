<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('system.roles.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('system.roles.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-col space-y-2">
        @include('partials.flash')

        <div class="flex items-center justify-between">
            <flux:input icon="magnifying-glass" :loading="false" :clearable="true" placeholder="{{ __('system.roles.search_roles') }}" wire:model.live.debounce.500ms="search" class="max-w-64" autocomplete="off" />
            @can('roles.create')
            <flux:button variant="primary" color="blue" size="sm" href="{{ route('roles.create') }}">{{ __('system.roles.buttons.create') }}</flux:button>
            @endcan
        </div>

        <x-tables.table>
            <x-slot name="thead">
                <tr class="tr">
                    <th scope="col" class="td">{{ __('system.roles.table.name') }}</th>
                    <th scope="col" class="td">{{ __('system.roles.table.description') }}</th>
                    <th scope="col" class="td" align="end">{{ __('system.roles.table.actions') }}</th>
                </tr>
            </x-slot>
            <x-slot name="tbody">
                @forelse ($this->roles as $role)
                    <tr class="tr tr-hover">
                        <td class="td">
                            <div class="flex flex-col gap-1">
                                <span class="text-base">{{ $role->display_name }}</span>
                                <span class="text-xs italic">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="td">
                            <span class="text-xs italic">{{ $role->description }}</span>
                        </td>
                        <td class="td text-right space-x-2">
                            @can('roles.edit')
                            <flux:button variant="primary" color="green" size="sm" href="{{ route('roles.edit', $role->id) }}">{{ __('system.roles.buttons.edit') }}</flux:button>
                            @endcan
                            @can('roles.delete')
                            <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $role->id }})">{{ __('system.roles.buttons.delete') }}</flux:button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr class="tr">
                        <td class="td text-center" colspan="3">
                            {{ __('system.roles.table.no_roles') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-tables.table>

        <flux:pagination :paginator="$this->roles" />
    </div>

    {{-- modal eliminar --}}
    <flux:modal name="delete-note" wire:model="deleteModalVisible" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <flux:fieldset>
                <flux:legend>{{ __('system.roles.delete.title') }}</flux:legend>
                <flux:description>{{ __('system.roles.delete.confirmation_message', ['name' => $current_name]) }}</flux:description>
                <div class="space-y-4">
                    <flux:input wire:model="name" :label="__('system.roles.delete.role_name')" type="text" placeholder="{{ $current_name }}" required autocomplete="off" />
                    <flux:input wire:model="password" :label="__('system.roles.delete.password')" type="password" required autocomplete="off" />
                    @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </flux:fieldset>


            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" color="yellow" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                @can('roles.delete')
                <flux:button size="sm" variant="danger" wire:click="destroyRole">{{ __('system.roles.delete.button') }}</flux:button>
                @endcan
            </div>
        </div>
    </flux:modal>
</div>
