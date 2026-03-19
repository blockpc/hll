<?php

use App\Models\Roster;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public bool $infantrySquads = false;

    public int $countSquads = 0;

    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Infantry> */
    public $infantries;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster;

        $this->infantries = $roster->infantrySquads()->get();
        $this->infantrySquads = $this->infantries->isNotEmpty();
        $this->countSquads = $this->infantries->count();
    }
};
