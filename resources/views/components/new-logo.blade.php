@blaze(fold: true)

<div class="flex items-center gap-3">
    <div class="flex h-12 w-12 items-center justify-center rounded-md border border-amber-500/40 bg-amber-500/10 text-sm font-bold text-amber-300">
        {{ config('app.sigla', 'HLL') }}
    </div>
    <div>
        <p class="text-xs font-semibold tracking-[0.2em] text-amber-300 uppercase">
            {{ config('app.subname', 'Tactical Roster Planner') }}
        </p>
        <h1 class="text-base font-bold text-black dark:text-white">
            {{ config('app.name', 'HLL Rosters') }}
        </h1>
    </div>
</div>
