<?php

use App\Models\Roster;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component
{
    public Roster $roster;

    #[Computed()]
    public function rosters()
    {

    }
};
