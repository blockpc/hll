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

    public function alertSuccess(string $message, string $title = '', int $time = 5000): void
    {
        $this->dispatch('show', $message, 'success', $title, $time)->to('alert');
    }

    public function alertError(string $message, string $title = '', int $time = 5000): void
    {
        $this->dispatch('show', $message, 'error', $title, $time)->to('alert');
    }

    public function alertWarning(string $message, string $title = '', int $time = 5000): void
    {
        $this->dispatch('show', $message, 'warning', $title, $time)->to('alert');
    }

    public function alertInfo(string $message, string $title = '', int $time = 5000): void
    {
        $this->dispatch('show', $message, 'info', $title, $time)->to('alert');
    }
}
