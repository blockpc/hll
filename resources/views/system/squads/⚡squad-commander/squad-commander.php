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
        $this->squadCommander = $roster->commandSquads()->first();
        $this->commandSquads = $this->squadCommander !== null;
        $this->countSquads = $this->commandSquads ? 1 : 0;
    }

    /**
     * @todo Implement show functionality
     */
    public function show(): void
    {
    }
};
