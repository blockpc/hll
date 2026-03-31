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
        $this->countSquads = $this->commandSquads ? 1 : 0;
    }

    /**
     * Removes a soldier from custom squads and re-renders the component.
     */
    public function remove_soldier(int $soldierId): void
    {
        if ($this->squadCommander) {
            $this->squadCommander->soldiers()->delete($soldierId);
            $this->reRender();
        }
    }

    public function addSoldier(int $squadId): void
    {
        $this->dispatch('open-add-soldier', $squadId);
    }
};
