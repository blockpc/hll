<?php

use App\Models\Roster;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component
{
    #[Computed()]
    public function rosters(): Collection|SupportCollection
    {
        return Roster::query()
            ->where('is_public', true)
            ->get();
    }
};
