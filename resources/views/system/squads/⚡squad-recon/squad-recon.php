<?php

use App\Models\Roster;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public int $countSquads = 0;

    public bool $buttons = true;

    #[On('re-render')]
    public function reRender(): void
    {
        $this->roster->refresh();
        $this->countSquads = $this->roster->reconSquads()->count();
    }

    public function addSoldier(int $squadId): void
    {
        $this->dispatch('open-add-soldier', $squadId);
    }
};
