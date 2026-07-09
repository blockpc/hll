<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('hll.clans.create.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.create.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="w-full">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <flux:input label="{{ __('hll.clans.form_fields.alias') }}" placeholder="{{ __('hll.clans.form_fields.alias_placeholder') }}" wire:model="alias" maxlength="8" required />

                    <flux:input label="{{ __('hll.clans.form_fields.name') }}" placeholder="{{ __('hll.clans.form_fields.name_placeholder') }}" wire:model.blur.live="name" maxlength="32" required />

                    <flux:input label="{{ __('hll.clans.form_fields.slug') }}" placeholder="{{ __('hll.clans.form_fields.slug_placeholder') }}" wire:model="slug" maxlength="255" />

                    <flux:textarea label="{{ __('hll.clans.form_fields.description') }}" placeholder="{{ __('hll.clans.form_fields.description_placeholder') }}" wire:model="description" maxlength="255" rows="4" />

                    <flux:input label="{{ __('hll.clans.form_fields.discord') }}" placeholder="{{ __('hll.clans.form_fields.discord_placeholder') }}" wire:model="discord" maxlength="255" />

                    @if ($this->canSelectOwner())
                    <div class="space-y-2">
                        <p class="text-xs italic text-yellow-500 dark:text-yellow-500">
                            {{ __('hll.clans.form_fields.owner_help') }}
                        </p>

                        <x-select2-single
                            name="users"
                            title="system.notifications.select_user"
                            :options="$this->users"
                            :selected_id="$selectedUserId"
                            :selected_name="$selectedUserName"
                            search="searchUser"
                            click="selectUser"
                        />

                        @error('owner_user_id')
                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif

                    <flux:separator variant="subtle" />

                    <div class="flex items-center justify-end space-x-2">
                        <flux:button variant="ghost" size="sm" href="{{ route('clans.table') }}">{{ __('Cancel') }}</flux:button>
                        <flux:button variant="primary" color="blue" size="sm" type="submit">{{ __('hll.clans.create.submit') }}</flux:button>
                    </div>
                </div>
                <div class="space-y-6">
                    <x-input-file-single name="logo" title="{{ __('hll.clans.form_fields.logo') }}" wire:model.live="logo" stacked>
                        <div class="relative overflow-hidden rounded-full border border-neutral-200 dark:border-neutral-700 h-32 w-32">
                            @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="{{ __('hll.clans.form_fields.logo') }}" class="h-full w-full object-cover">
                            @else
                            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                            @endif
                        </div>
                    </x-input-file-single>

                    <x-input-file-single name="image" title="{{ __('hll.clans.form_fields.image') }}" wire:model.live="image" stacked>
                        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 h-36 w-72">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" alt="{{ __('hll.clans.form_fields.image') }}" class="h-full w-full object-cover">
                            @else
                            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                            @endif
                        </div>
                    </x-input-file-single>
                </div>
            </div>
        </form>
    </div>
</div>
