<?php

use App\Models\Roster;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public bool $hasInfantrySquads = false;

    public int $countSquads = 0;

    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Infantry> */
    public Collection $infantries;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster;

        $this->infantries = $roster->infantrySquads()->with(['soldiers'])->get();
        $this->hasInfantrySquads = $this->infantries->isNotEmpty();
        $this->countSquads = $this->infantries->count();
    }
};
