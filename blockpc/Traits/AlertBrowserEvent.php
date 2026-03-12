<?php

declare(strict_types=1);

namespace Blockpc\Traits;

trait AlertBrowserEvent
{
    public function alert(string $message, string $type = 'success', string $title = '', int $time = 5000): void
    {
        $this->dispatch('show', $message, $type, $title, $time)->to('alert');
    }

    public function flash(string $message, string $type = 'success'): void
    {
        session()->flash($type, $message);
    }
}
