<div class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('hll.clans.create.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('hll.clans.create.subtitle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="w-full max-w-xl">
        <form wire:submit.prevent="save" class="w-full space-y-6">
            <flux:input label="{{ __('hll.clans.create.form.alias') }}" placeholder="{{ __('hll.clans.create.form.alias_placeholder') }}" wire:model="alias" maxlength="32" required />

            <flux:input label="{{ __('hll.clans.create.form.name') }}" placeholder="{{ __('hll.clans.create.form.name_placeholder') }}" wire:model.blur.live="name" maxlength="64" required />

            <flux:input label="{{ __('hll.clans.create.form.slug') }}" placeholder="{{ __('hll.clans.create.form.slug_placeholder') }}" wire:model="slug" maxlength="64" />

            <flux:textarea label="{{ __('hll.clans.create.form.description') }}" placeholder="{{ __('hll.clans.create.form.description_placeholder') }}" wire:model="description" maxlength="255" rows="4" />

            <flux:input label="{{ __('hll.clans.create.form.discord') }}" placeholder="{{ __('hll.clans.create.form.discord_placeholder') }}" wire:model="discord" maxlength="64" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-input-file-single name="logo" title="{{ __('hll.clans.create.form.logo') }}" wire:model.live="logo">
                    <div class="relative overflow-hidden rounded-full border border-neutral-200 dark:border-neutral-700 h-16 w-16">
                        @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" alt="{{ __('hll.clans.create.form.logo') }}" class="h-full w-full object-cover">
                        @else
                        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                        @endif
                    </div>
                </x-input-file-single>

                <x-input-file-single name="image" title="{{ __('hll.clans.create.form.image') }}" wire:model.live="image">
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 h-16 w-16">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="{{ __('hll.clans.create.form.image') }}" class="h-full w-full object-cover">
                        @else
                        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                        @endif
                    </div>
                </x-input-file-single>
            </div>

            @if ($this->canSelectOwner())
            <div class="space-y-2">
                <p class="text-xs italic text-yellow-500 dark:text-yellow-500">
                    {{ __('hll.clans.create.form.owner_help') }}
                </p>

                <x-select2-single
                    name="users"
                    title="{{ __('system.notifications.select_user') }}"
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

            <div class="flex items-center justify-end space-x-2">
                <flux:button variant="primary" color="gray" size="sm" href="{{ route('clans.table') }}">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" color="blue" size="sm" type="submit">{{ __('hll.clans.create.submit') }}</flux:button>
            </div>
        </form>
    </div>
</div>
