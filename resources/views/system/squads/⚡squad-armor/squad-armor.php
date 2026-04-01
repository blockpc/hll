<?php

use App\Models\Roster;
use App\Models\Squad;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
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
    public function addSoldier(int $squadId): void
    {
        $squad = $this->roster->armorSquads()->findOrFail($squadId);
        if ($squad->isFull()) {
            $this->alert(__('hll.squads.squad_armor.full_squad'), 'warning', __('hll.squads.squad_armor.title'));

            return;
        }
        $this->dispatch('open-add-soldier', $squadId);
    }

    private function calculateSquadCount(): void
    {
        $this->countSquads = $this->roster->armorSquads()->count();
    }
};
