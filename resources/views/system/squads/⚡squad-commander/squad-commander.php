<?php

use App\Models\Roster;
use App\Models\Squad;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public bool $commandSquads = false;

    public int $countSquads = 0;

    public ?Squad $squadCommander = null;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster;
        $this->squadCommander = $roster->commandSquads()->first();
        $this->commandSquads = $this->squadCommander !== null;
        $this->countSquads = $this->commandSquads ? 1 : 0;
    }

    #[On('re-render')]
    public function reRender(): void
    {
        $this->roster->refresh();
        $this->countSquads = $this->customSquads->count();
    }

    /**
     * Removes a soldier from the squad commander and re-renders the component.
     */
    public function remove_soldier(int $soldierId): void
    {
        $squad = $this->roster->customSquads()->whereHas('soldiers', function ($query) use ($soldierId) {
            $query->where('soldier_id', $soldierId);
        })->first();

        if ($squad) {
            $squad->soldiers()->detach($soldierId);
            $this->reRender();
        }
    }
};
