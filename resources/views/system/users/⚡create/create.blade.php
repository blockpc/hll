
<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('system.users.create.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('system.users.create.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="w-full">
        @include('partials.flash')

        <form wire:submit.prevent="save" class="w-full space-y-6" autocomplete="off">
            <div class="w-full max-w-lg space-y-6">
                <flux:fieldset>
                    <flux:legend>{{ __('system.users.create.general.title') }}</flux:legend>
                    <flux:description>{{ trans('system.users.create.general.description') }}</flux:description>

                    <flux:input wire:model="name" :label="__('system.users.create.form.name')" type="text" required autofocus autocomplete="off" />

                    <flux:input wire:model="email" :label="__('system.users.create.form.email')" type="email" required autocomplete="off" />
                </flux:fieldset>

                <flux:fieldset>
                    <flux:legend>{{ __('system.users.create.passwords.title') }}</flux:legend>
                    <flux:description>{{ trans('system.users.create.passwords.description') }}</flux:description>

                    <div class="flex items-center space-x-4 mb-4">
                        <div>{{ __('system.users.create.passwords.auto_generate_label') }}</div>
                        <x-toggle name="create_password_user" wire:model.live="auto_password" />
                    </div>

                    <div wire:key="manual-password-fields" @class(['space-y-4', 'hidden' => $auto_password])>
                        <flux:input wire:model="password" :label="__('system.users.create.form.password')" type="password" autocomplete="off" viewable :disabled="$auto_password" />

                        <flux:input wire:model="password_confirmation" :label="__('system.users.create.form.password_confirmation')" type="password" autocomplete="off" viewable :disabled="$auto_password" />
                    </div>
                </flux:fieldset>

                <flux:fieldset>
                    <flux:legend>{{ __('system.users.create.roles.title') }}</flux:legend>
                    <flux:description>{{ trans('system.users.create.roles.description') }}</flux:description>

                    <div>
                        <x-select2-multiple
                            name="roles"
                            title="system.users.create.form.select_roles"
                            :options="$this->roles"
                            :selected_ids="$selectedRolesIds"
                            search="searchRole"
                            click="selectRole"
                        />
                    </div>

                    <div class="mt-2">
                        @foreach ($selectedRolesNames as $roleId => $roleName)
                            <flux:badge size="sm" class="flex items-center space-x-1 w-auto!">
                                <div>{{ $roleName }}</div>
                                <flux:button variant="ghost" size="xs" icon="x-mark" wire:click="deleteRoleId({{ $roleId }})" />
                            </flux:badge>
                        @endforeach
                    </div>
                </flux:fieldset>

                <flux:fieldset>
                    <flux:legend>{{ __('system.users.create.permissions.title') }}</flux:legend>
                    <flux:description>{{ trans('system.users.create.permissions.description') }}</flux:description>

                    <div>
                        <x-select2-multiple
                            name="permissions"
                            title="system.users.create.form.select_permissions"
                            :options="$this->permissions"
                            :selected_ids="$selectedPermissionsIds"
                            search="searchPermission"
                            click="selectPermission"
                        />
                    </div>

                    <div class="mt-2">
                        @foreach ($selectedPermissionsNames as $permissionId => $permissionName)
                            <flux:badge size="sm" class="flex items-center space-x-1 w-auto!">
                                <div>{{ $permissionName }}</div>
                                <flux:button variant="ghost" size="xs" icon="x-mark" wire:click="deletePermissionId({{ $permissionId }})" />
                            </flux:badge>
                        @endforeach
                    </div>
                </flux:fieldset>

                <flux:separator variant="subtle" class="my-4" />

                <div class="flex items-center justify-between">
                    <div>
                        <flux:button variant="subtle" href="{{ route('users.table') }}" class="w-full">
                            {{ __('system.users.back_to_table') }}
                        </flux:button>
                    </div>
                    <div class="flex items-center justify-end">
                        @can('users.create')
                        <flux:button variant="primary" type="submit" color="blue" class="w-full" data-test="create-user-button">
                            {{ __('system.users.create.save') }}
                        </flux:button>
                        @endcan
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
