<?php

use App\Models\Roster;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public bool $buttons = false;

    /** @var Collection<int, \App\Models\Squad>|null */
    public ?Collection $customSquads = null;

    public int $countSquads = 0;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster->loadMissing('customSquads');
        $this->customSquads = $this->roster->customSquads;
        $this->countSquads = $this->customSquads->count();
    }

    public function addSoldier(int $squadId): void
    {
        $this->dispatch('open-add-soldier', $squadId);
    }
};
