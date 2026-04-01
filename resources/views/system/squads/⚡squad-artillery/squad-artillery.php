<?php

use App\Models\Roster;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public int $countSquads = 0;

    public bool $buttons = true;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster;
        $this->countSquads = $this->roster->artillerySquads()->count();
    }
};
