@props([
    'name',
    'title',
    'options',
    'empty_values' => 'Sin registros encontrados',
    'selected_id' => null,
    'selected_name' => null,
    'search_by' => 'Buscar...'
])

<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col text-xs font-semibold']) }} id="{{ $name }}-select2-single">
    <div
        class="w-full relative"
        x-data="{
            open: false,
            dropUp: false,
            updatePosition() {
                this.$nextTick(() => {
                    const trigger = this.$refs.trigger;
                    const panel = this.$refs.panel;

                    if (!trigger || !panel) return;

                    const rect = trigger.getBoundingClientRect();
                    const panelHeight = panel.offsetHeight || 250;

                    const spaceBelow = window.innerHeight - rect.bottom;
                    const spaceAbove = rect.top;

                    this.dropUp = spaceBelow < panelHeight && spaceAbove > spaceBelow;
                });
            },
            toggle() {
                this.open = !this.open;

                if (this.open) {
                    this.updatePosition();
                }
            }
        }"
        x-on:click.away="open = false"
        x-on:resize.window="if (open) updatePosition()"
        x-on:scroll.window="if (open) updatePosition()"
        x-on:keydown.escape="open = false"
        role="combobox"
        :aria-expanded="open"
    >
        <div class="flex space-x-2">
            <button
                x-ref="trigger"
                type="button"
                aria-haspopup="listbox"
                :aria-expanded="open"
                class="inline-flex items-center px-2 py-1 text-xs whitespace-nowrap font-semibold uppercase btn-select justify-between w-full bg-transparent border rounded border-gray-500 p-2 h-8"
                x-on:click="toggle()"
            >
                <span>{{ __($selected_name ?: $title) }}</span>
                <div :class="open ? 'rotate-180' : ''">
                    <flux:icon.chevron-down class="size-4" />
                </div>
            </button>
        </div>

        <div
            x-ref="panel"
            class="absolute z-10 w-full shadow-md bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-100 border-l border-r border-zinc-500"
            :class="dropUp
                ? 'bottom-full mb-1 rounded-t border-t border-b'
                : 'top-full mt-1 rounded-b border-b'"
            x-show="open"
            x-cloak
        >
            <ul role="listbox" class="list-reset p-2 max-h-40 text-sm scrollbar-thin scrollbar-thumb-zinc-400 scrollbar-track-zinc-300 overflow-y-auto space-y-1">
                <li class="sticky top-0 bg-zinc-100 dark:bg-zinc-700 mb-2">
                    <flux:input
                        size="sm"
                        icon="magnifying-glass"
                        :loading="false"
                        :clearable="true"
                        placeholder="{{ $search_by }}"
                        wire:model.live.debounce.500ms="{{ $attributes->get('search') }}"
                    />
                </li>

                @forelse ($options as $option_id => $option_name)
                    <li
                        @class([
                            'bg-zinc-300 dark:bg-zinc-800/50' => $option_id === $selected_id
                        ])
                        id="option-{{ $option_id }}"
                        wire:click="{{ $attributes->get('click') }}({{ json_encode($option_id) }})"
                        x-on:click="open = false"
                        x-on:keydown.enter.prevent="$wire.{{ $attributes->get('click') }}({{ json_encode($option_id) }}); open = false"
                        x-on:keydown.space.prevent="$wire.{{ $attributes->get('click') }}({{ json_encode($option_id) }}); open = false"
                        x-on:keydown.arrow-down.prevent="let next = $el.nextElementSibling; while (next && !next.id.startsWith('option-')) { next = next.nextElementSibling; } if (next) { next.focus(); }"
                        x-on:keydown.arrow-up.prevent="let previous = $el.previousElementSibling; while (previous && !previous.id.startsWith('option-')) { previous = previous.previousElementSibling; } if (previous) { previous.focus(); }"
                        role="option"
                        tabindex="0"
                        :aria-selected="{{ $option_id === $selected_id ? 'true' : 'false' }}"
                    >
                        <div class="p-2 w-full hover:bg-zinc-300 hover:dark:bg-zinc-600 flex justify-between cursor-pointer text-xs">
                            <span>{{ $option_name }}</span>
                            @if ($option_id === $selected_id)
                                <flux:icon.check class="w-4 h-4" />
                            @endif
                        </div>
                    </li>
                @empty
                    <li x-on:click="open = false" wire:click="$set('{{ $attributes->get('search') }}', null)">
                        <p class="p-2 block dark:text-red-400 text-red-800 hover:bg-red-200 cursor-pointer">
                            {{ $empty_values }}
                        </p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
