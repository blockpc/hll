<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public bool $open = false;

    public string $message = '';

    public string $class_alert = '';

    public string $title = '';

    public int $time = 5000;

    #[On('show')]
    public function show(string $message, string $alert = "info", string $title = "", int $time = 5000): void
    {
        $this->open = true;
        $this->message = $message;
        $this->class_alert = $this->match_type($alert);
        $this->title = $title;
        $this->time = $time;
    }

    public function hide(): void
    {
        $this->reset();
    }

    private function match_type(string $alert): string
    {
        return match ($alert) {
            'success' => 'alert alert-success',
            'error' => 'alert alert-danger',
            'warning' => 'alert alert-warning',
            'info' => 'alert alert-info',
            default => 'alert alert-info',
        };
    }
};
?>

<div>
    <div class="fixed top-4 inset-x-0 z-20" x-data="{
        open: @entangle('open').live,
        class_alert: @entangle('class_alert').live,
        time: @entangle('time').live,
        timeoutId: null
    }"
    x-init="$watch('open', value => {
        if (! value) {
            return;
        }

        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => open = false, time);
    })">
        <div class="w-3/4 mx-auto cursor-pointer" :class="class_alert" x-show="open" x-on:click="open=false" x-transition>
            <div class="flex">
                <div class="flex-1">
                    @if ( $title )
                    <p class="font-bold text-base">{{$title}}</p>
                    @endif
                    <p class="text-sm font-roboto font-medium">{{ $message }}</p>
                </div>
                <button type="button" class="btn-sm" wire:click="hide()">
                    <flux:icon.x-mark />
                </button>
            </div>
        </div>
    </div>
</div>
