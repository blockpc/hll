<?php

use App\Models\Roster;
use App\Models\Squad;
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

    public function openAddSoldierModal(int $squadId): void
    {
        $squad = Squad::findOrFail($squadId);
        if ($squad->isFull()) {
            $this->alert(__('hll.squads.squad_recon.fully_squad'), 'warning', __('hll.squads.squad_recon.title'));

            return;
        }
        $this->dispatch('open-add-soldier', $squadId);
    }

    private function calculateSquadCount(): void
    {
        $this->countSquads = $this->roster->reconSquads()->count();
    }
};
