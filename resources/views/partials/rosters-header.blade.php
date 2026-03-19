<div class="flex items-start justify-between space-x-6">
    <x-header-clan :clan="$clan" :title="__('hll.clans.rosters.template.roster_title', ['name' => $roster->name])" :subtitle="__('hll.clans.rosters.template.subtitle')" />
    <div class="flex items-center space-x-2">
        <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}">
            {{ __('hll.clans.rosters.back_to_clan') }}
        </flux:button>

        <flux:button variant="ghost" size="sm" href="{{ route('rosters.table', $clan->slug) }}">
            {{ __('hll.clans.rosters.back_to_rosters') }}
        </flux:button>
    </div>
</div>
