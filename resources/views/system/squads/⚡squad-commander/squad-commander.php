<?php

use App\Models\Roster;
use App\Models\Squad;
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
        $commanderSquads = $roster->commandSquads()->get();
        $this->countSquads = $commanderSquads->count();
        $this->commandSquads = $this->countSquads > 0;
        $this->squadCommander = $commanderSquads->first();
    }

    public function show(): void
    {
        // TODO: Implement show functionality
    }
};
