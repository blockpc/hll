<?php

use App\Models\Roster;
use App\Models\SquadSoldier;
use Blockpc\Traits\AlertBrowserEvent;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;

    public Roster $roster;

    public int $countSquads = 0;

    public bool $displayControls = true;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster;
        $this->calculateSquadCount();
    }

    #[On('re-render')]
    public function reRender(): void
    {
        $this->roster->refresh();
        $this->calculateSquadCount();
    }

    /**
     * Opens the add soldier modal for the specified squad.
     * Shows a warning alert and returns early if the squad is full.
     */
    public function openAddSoldierModal(int $squadId): void
    {
        $this->dispatch('open-add-soldier', $squadId);
    }

    private function calculateSquadCount(): void
    {
        $this->countSquads = $this->roster->artillerySquads()->count();
    }

    /**
     * Removes a soldier from the squad commander and re-renders the component.
     */
    public function removeSoldier(int $soldierId): void
    {
        $soldier = SquadSoldier::findOrFail($soldierId);
        $name = $soldier->display_name;

        $soldier->delete();
        $this->reRender();
        $this->alert(__('hll.squads.soldier_removed', ['name' => $name]), 'success', __('hll.squads.squad_artillery.title'));
    }
};
