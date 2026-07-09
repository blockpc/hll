
<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('system.users.edit.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('system.users.edit.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="w-full">
        @include('partials.flash')

        <form wire:submit.prevent="save" class="w-full space-y-6" autocomplete="off">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <flux:fieldset>
                        <flux:legend>{{ __('system.users.edit.general.title') }}</flux:legend>
                        <flux:description>{{ __('system.users.edit.general.description') }}</flux:description>

                        <flux:input size="sm" wire:model="name" :label="__('system.users.edit.form.name')" type="text" required autofocus autocomplete="off" />

                        <flux:input size="sm" wire:model="email" :label="__('system.users.edit.form.email')" type="email" required autocomplete="off" />
                    </flux:fieldset>

                    <flux:fieldset>
                        <flux:legend>{{ __('system.users.edit.passwords.title') }}</flux:legend>
                        <flux:description>{{ __('system.users.edit.passwords.description') }}</flux:description>

                        <flux:input size="sm" wire:model="password" :label="__('system.users.edit.form.password')" type="password" autocomplete="off" viewable />

                        <flux:input size="sm" wire:model="password_confirmation" :label="__('system.users.edit.form.password_confirmation')" type="password" autocomplete="off" viewable />
                    </flux:fieldset>

                    <flux:fieldset>
                        <flux:legend>{{ __('system.users.edit.user_email_verification.title') }}</flux:legend>
                        <flux:description>{{ __('system.users.edit.user_email_verification.description') }}</flux:description>
                        @if (session()->has('success-email-change-verified'))
                            <flux:callout variant="success" class="mb-2">
                                {{ session('success-email-change-verified') }}
                            </flux:callout>
                        @endif
                        @if (session()->has('success-email-change-unverified'))
                            <flux:callout variant="success" class="mb-2">
                                {{ session('success-email-change-unverified') }}
                            </flux:callout>
                        @endif
                        @if (session()->has('success-email-change-resend'))
                            <flux:callout variant="success" class="mb-2">
                                {{ session('success-email-change-resend') }}
                            </flux:callout>
                        @endif
                        @if (session()->has('error-email-change-resend'))
                            <flux:callout variant="danger" class="mb-2">
                                {{ session('error-email-change-resend') }}
                            </flux:callout>
                        @endif

                        @if ($user_has_verified_email)
                            <flux:badge color="green" class="w-full">{{ __('system.users.edit.user_email_verification.verified') }}</flux:badge>
                            <flux:button variant="outline" size="sm" class="btn-warning mt-2" wire:click="markEmailAsUnverified">
                                {{ __('system.users.edit.user_email_verification.mark_as_unverified') }}
                            </flux:button>
                        @else
                            <flux:badge color="red" class="w-full">{{ __('system.users.edit.user_email_verification.not_verified') }}</flux:badge>
                            <flux:button variant="outline" size="sm" class="btn-success mt-2" wire:click="markEmailAsVerified">
                                {{ __('system.users.edit.user_email_verification.mark_as_verified') }}
                            </flux:button>
                            <flux:button variant="outline" size="sm" class="btn-info mt-2" wire:click="resendVerificationEmail">
                                {{ __('system.users.edit.user_email_verification.resend_verification_email') }}
                            </flux:button>
                        @endif
                    </flux:fieldset>
                </div>

                <div class="space-y-6">
                    <flux:fieldset>
                        <flux:legend>{{ __('system.users.edit.roles.title') }}</flux:legend>
                        <flux:description>{{ __('system.users.edit.roles.description') }}</flux:description>

                        <div>
                            <x-select2-multiple
                                name="roles"
                                title="system.users.edit.form.select_roles"
                                :options="$this->roles"
                                :selected_ids="$selectedRolesIds"
                                search="searchRole"
                                click="selectRole"
                            />
                        </div>

                        <div class="mt-2">
                            @foreach ($user->roles as $role)
                                <flux:badge size="sm" class="flex items-center space-x-1 w-auto!">
                                    <div>{{ $role->display_name }}</div>
                                    <flux:button variant="ghost" size="xs" icon="x-mark" wire:click="deleteRoleId({{ $role->id }})" />
                                </flux:badge>
                            @endforeach
                        </div>
                    </flux:fieldset>

                    <flux:fieldset>
                        <flux:legend>{{ __('system.users.edit.permissions.title') }}</flux:legend>
                        <flux:description>{{ __('system.users.edit.permissions.description') }}</flux:description>

                        <div>
                            <x-select2-multiple
                                name="permissions"
                                title="system.users.edit.form.select_permissions"
                                :options="$this->permissions"
                                :selected_ids="$selectedPermissionsIds"
                                search="searchPermission"
                                click="selectPermission"
                            />
                        </div>

                        <div class="mt-2">
                            @foreach ($user->permissions as $permission)
                                <flux:badge size="sm" class="flex items-center space-x-1 w-auto!">
                                    <div>{{ $permission->display_name }}</div>
                                    <flux:button variant="ghost" size="xs" icon="x-mark" wire:click="deletePermissionId({{ $permission->id }})" />
                                </flux:badge>
                            @endforeach
                        </div>
                    </flux:fieldset>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <flux:separator variant="subtle" />

                    <div class="flex items-center justify-between">
                        <div>
                            <flux:button variant="subtle" href="{{ route('users.table') }}" class="w-full">
                                {{ __('system.users.back_to_table') }}
                            </flux:button>
                        </div>
                        <div class="flex items-center justify-end">
                            @can('users.edit')
                            <flux:button variant="primary" type="submit" color="green" class="w-full" data-test="edit-user-button">
                                {{ __('system.users.edit.save') }}
                            </flux:button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
