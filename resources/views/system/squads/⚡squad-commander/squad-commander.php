<?php

use App\Models\Roster;
use App\Models\Squad;
use App\Models\SquadSoldier;
use Blockpc\Traits\AlertBrowserEvent;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;

    public Roster $roster;

    public bool $commandSquads = false;

    public int $countSquads = 0;

    public ?Squad $squadCommander = null;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster;
        $this->squadCommander = $roster->commandSquads()->first();
        $this->commandSquads = $this->squadCommander !== null;
        $this->calculateSquadCount();
    }

    #[On('re-render')]
    public function reRender(): void
    {
        $this->roster->refresh();
        $this->calculateSquadCount();
    }

    /**
     * Removes a soldier from custom squads and re-renders the component.
     */
    public function removeSoldier(int $soldierId): void
    {
        $soldier = SquadSoldier::findOrFail($soldierId);
        $name = $soldier->display_name;

        $soldier->delete();
        $this->reRender();
        $this->alert(__('hll.squads.soldier_removed', ['name' => $name]), 'success', __('hll.squads.squad_command.title'));
    }

    /**
     * Opens the add soldier modal for the specified squad.
     * Shows a warning alert and returns early if the squad is full.
     */
    public function openAddSoldierModal(int $squadId): void
    {
        $squad = $this->roster->commandSquads()->first();
        if (! $squad) {
            return;
        }
        if ($squad->isFull()) {
            $this->alert(__('hll.squads.squad_command.full_squad'), 'warning', __('hll.squads.squad_command.title'));

            return;
        }
        $this->dispatch('open-add-soldier', $squadId);
    }

    private function calculateSquadCount(): void
    {
        $this->countSquads = $this->commandSquads ? 1 : 0;
    }
};
