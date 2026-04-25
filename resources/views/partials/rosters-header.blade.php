<div class="flex items-start justify-between mb-4">
    <x-header-clan :clan="$clan" :title="__('hll.clans.rosters.template.roster_title', ['name' => $roster->name])" :subtitle="__('hll.clans.rosters.template.subtitle')" />
    <div class="flex flex-col justify-between h-24">
        <div class="flex justify-end items-center space-x-2">
            <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}" wire:navigate>
                {{ __('hll.clans.rosters.back_to_clan') }}
            </flux:button>

            <flux:button variant="ghost" size="sm" href="{{ route('rosters.table', $clan->slug) }}" wire:navigate>
                {{ __('hll.clans.rosters.back_to_rosters') }}
            </flux:button>
        </div>
        <div class="flex justify-end items-center space-x-2 mt-auto">
            <div x-data="{ text: @entangle('link'), copied: false }" class="flex flex-col gap-2">
                <div class="relative flex items-center">
                    {{-- <input type="text" :value="text" readonly class="w-full pr-12 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"> --}}

                    <flux:button type="button" variant="outline" size="xs" @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)">
                        <span x-show="!copied">Compartir URL</span>
                        <span x-show="copied">✓</span>
                    </flux:button>

                    {{-- <button @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                            class="absolute right-1 px-2 py-1 text-xs font-semibold text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <span x-show="!copied">Compartir URL</span>
                        <span x-show="copied">✓</span>
                    </button> --}}
                </div>
            </div>
            @can('update', $roster)
                <x-nav-button-navigate :route="'rosters.edit'" :route-params="[$clan->slug, $roster->uuid]">
                    {{ __('hll.clans.rosters.edit_button') }}
                </x-nav-button-navigate>

                <x-nav-button-navigate :route="'rosters.template.manage'" :route-params="[$clan->slug, $roster->uuid]">
                    {{ __('hll.clans.rosters.roster_manage') }}
                </x-nav-button-navigate>

                <x-nav-button-navigate :route="'rosters.template.map'" :route-params="[$clan->slug, $roster->uuid]">
                    {{ __('hll.clans.rosters.roster_map') }}
                </x-nav-button-navigate>
            @endcan
        </div>
    </div>
</div>
