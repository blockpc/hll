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
        $query = $roster->commandSquads();
        $this->countSquads = $query->count();
        $this->commandSquads = $this->countSquads > 0;
        $this->squadCommander = $this->commandSquads ? $query->first() : null;
    }

    /**
     * @todo Implement show functionality
     */
    public function show(): void
    {
    }
};
