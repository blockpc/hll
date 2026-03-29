<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <flux:link :href="route('dashboard')" wire:navigate>
                    <x-new-logo />
                </flux:link>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('notes.index')" :current="request()->routeIs('notes.index')" wire:navigate>
                        {{ __('User Notes') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            @canany(['clans.index'])
            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('hll.clans.menu')" class="grid">
                    @can('clans.index')
                    <flux:sidebar.item icon="home" :href="route('clans.table')" :current="request()->routeIs('clans.*')" wire:navigate>
                        {{ __('hll.clans.index.submenu') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            </flux:sidebar.nav>
            @endcanany

            <flux:spacer />

            @canany(['users.index', 'roles.index', 'permissions.index'])
            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('System')" class="grid">
                    @can('users.index')
                    <flux:sidebar.item icon="lock-closed" :href="route('users.table')" :current="request()->routeIs('users.*')" wire:navigate>
                        {{ __('system.users.menu') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('roles.index')
                    <flux:sidebar.item icon="lock-closed" :href="route('roles.table')" :current="request()->routeIs('roles.*')" wire:navigate>
                        {{ __('system.roles.menu') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('permissions.index')
                    <flux:sidebar.item icon="lock-closed" :href="route('permissions.table')" :current="request()->routeIs('permissions.*')" wire:navigate>
                        {{ __('system.permissions.menu') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            </flux:sidebar.nav>
            @endcanany

            <flux:sidebar.nav>
                <livewire:notifications.open-close-panel />
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <flux:navbar class="w-full">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                <flux:spacer />
                <livewire:notes.create-dashboard-note />
                <livewire:notifications.open-close-panel />
                <div class="lg:hidden!">
                    <x-desktop-user-menu :name="auth()->user()->name" />
                </div>
            </flux:navbar>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
