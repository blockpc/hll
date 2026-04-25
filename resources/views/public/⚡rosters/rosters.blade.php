<div>
    <section class="mx-auto max-w-7xl gap-12 px-6 py-16 lg:px-8 lg:py-24">
        <h1 class="text-2xl font-bold mb-4">{{ __('rosters.publics') }}</h1>

        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                @forelse($this->rosters as $roster)
                    <a href="{{ route('public.rosters.show', $roster) }}" class="rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-4 mb-2">
                                @if ($roster->clan->logo)
                                <img src="{{ $roster->clan->logo_url }}" alt="{{ $roster->clan->name }}" class="w-16 h-16 rounded-full object-cover">
                                @else
                                <div class="relative w-16 h-16 rounded-full flex items-center justify-center">
                                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20 rounded-full border border-amber-500/30" />
                                    <span class="text-gray-300 text-sm font-semibold">{{ $roster->clan->alias }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="flex flex-col gap-1">
                                <h2 class="text-xl font-semibold">{{ $roster->name }}</h2>
                                <p class="text-sm text-gray-300">{{ $roster->description }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p>{{ __('rosters.no_results') }}</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
