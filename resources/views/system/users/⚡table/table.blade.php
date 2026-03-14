
<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('system.users.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('system.users.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-col space-y-2">
        @include('partials.flash')

        <div class="flex items-center justify-between">
            <flux:input icon="magnifying-glass" :loading="false" :clearable="true" placeholder="{{ __('system.users.search_users') }}" wire:model.live.debounce.500ms="search" class="max-w-64" autocomplete="off" />
            @can('users.create')
            <flux:button variant="primary" color="blue" size="sm" href="{{ route('users.create') }}">{{ __('system.users.buttons.create') }}</flux:button>
            @endcan
        </div>

        <x-tables.table>
            <x-slot name="thead">
                <tr class="tr">
                    <th scope="col" class="td">{{ __('system.users.table.name') }}</th>
                    <th scope="col" class="td">{{ __('system.users.table.email') }}</th>
                    <th scope="col" class="td">{{ __('system.users.table.roles') }}</th>
                    <th scope="col" class="td">{{ __('system.users.table.permissions') }}</th>
                    <th scope="col" class="td" align="end">{{ __('system.users.table.actions') }}</th>
                </tr>
            </x-slot>
            <x-slot name="tbody">
                @forelse ($this->users as $user)
                    <tr class="tr tr-hover">
                        <td class="td">
                            <span class="text-xs italic">{{ $user->name }}</span>
                        </td>
                        <td class="td">
                            <span class="text-xs italic">{{ $user->email }}</span>
                        </td>
                        <td class="td">
                            <div class="flex gap-1">
                                @foreach ($user->roles as $role)
                                    <flux:badge size="sm" class="w-auto!">{{ $role->display_name }}</flux:badge>
                                @endforeach
                            </div>
                        </td>
                        <td class="td">
                            <flux:badge size="sm" class="w-auto!">{{ $user->permissions_count }}</flux:badge>
                        </td>
                        <td class="td text-right space-x-2">
                            @can('users.edit')
                            <flux:button variant="primary" color="green" size="sm" href="{{ route('users.edit', $user->id) }}">{{ __('system.users.buttons.edit') }}</flux:button>
                            @endcan
                            @can('users.delete')
                            <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $user->id }})">{{ __('system.users.buttons.delete') }}</flux:button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr class="tr">
                        <td class="td text-center" colspan="5">
                            {{ __('system.users.table.no_users') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-tables.table>

        <flux:pagination :paginator="$this->users" />
    </div>

    {{-- modal eliminar --}}
    <flux:modal name="delete-user" wire:model="deleteModalVisible" class="w-1/2" :closable="false">
        <div class="space-y-4">
            <flux:fieldset>
                <flux:legend>{{ __('system.users.delete.title') }}</flux:legend>
                <flux:description>{{ trans('system.users.delete.confirmation_message', ['name' => $current_name]) }}</flux:description>
                <div class="space-y-4">
                    <flux:input wire:model="name" :label="__('system.users.delete.user_name')" type="text" placeholder="{{ $current_name }}" required autocomplete="off" />
                    <flux:input wire:model="password" :label="__('system.users.delete.password')" type="password" required autocomplete="off" />
                    @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </flux:fieldset>


            <div class="flex justify-end gap-2">
                <flux:button size="sm" variant="primary" color="yellow" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                @can('users.delete')
                <flux:button size="sm" variant="danger" wire:click="destroyUser">{{ __('system.users.delete.button') }}</flux:button>
                @endcan
            </div>
        </div>
    </flux:modal>
</div>
