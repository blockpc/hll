<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('system.roles.edit.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('system.roles.edit.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:heading class="sr-only">{{ __('system.roles.edit.title') }}</flux:heading>

    <div class="w-full max-w-xl">
        <form wire:submit.prevent="save" class="w-full space-y-6">
            <flux:input wire:model="display_name" :label="__('system.roles.edit.form.display_name')" type="text" required autofocus autocomplete="display_name" />
            <flux:textarea wire:model="description" :label="__('system.roles.edit.form.description')" type="text" required autocomplete="description" />

            <div class="flex flex-col space-y-2">
                <flux:label>{{ __('system.roles.edit.form.select_permissions') }}</flux:label>
                <div class="flex justify-between items-center">
                    <flux:input wire:model.live="permissions_search" :placeholder="__('system.permissions.search-permissions')" type="text" class="max-w-64" />
                    <flux:select wire:model.live="key" class="w-48">
                        <flux:select.option value="">{{ __('system.permissions.select_key') }}</flux:select.option>
                        @foreach ($this->keywords as $keyValue => $keyName)
                            <flux:select.option value="{{ $keyValue }}">{{ __('system.permissions.keys.'.$keyName) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="mt-2 flex flex-col space-y-1 max-h-60 overflow-y-auto">
                    @foreach ($this->permissions as $permission)
                        <div wire:key="permission-option-{{ $permission->id }}" class="flex items-center justify-between px-2 py-1 tr-hover" data-test="permission-{{ $permission->name }}">
                            <flux:checkbox label="{{ $permission->display_name }}" value="{{ $permission->name }}" wire:model="permissions_selecteds" data-test="permission-{{ $permission->name }}" />
                            <div class="text-xs italic">{{ __('system.permissions.keys.'.$permission->key) }}</div>
                        </div>
                    @endforeach
                </div>
                @error('permissions_selecteds') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <flux:button variant="subtle" href="{{ route('roles.table') }}" class="w-full">
                        {{ __('system.roles.back_to_table') }}
                    </flux:button>
                </div>
                <div class="flex items-center justify-end">
                    @can('roles.edit')
                    <flux:button variant="primary" type="submit" color="blue" class="w-full" data-test="edit-role-button">
                        {{ __('system.roles.edit.save') }}
                    </flux:button>
                    @endcan
                </div>
            </div>
        </form>
    </div>
</div>
