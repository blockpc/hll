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

    public function openAddSoldierModal(int $squadId): void
    {
        $this->dispatch('open-add-soldier', $squadId);
    }

    private function calculateSquadCount(): void
    {
        $this->countSquads = $this->roster->reconSquads()->count();
    }

    public function remove_soldier(int $soldierId): void
    {
        $sodier = SquadSoldier::findOrFail($soldierId);
        $name = $sodier->display_name;

        if ($sodier) {
            $sodier->delete();
            $this->reRender();
            $this->alert(__('hll.squads.soldier_removed', ['name' => $name]), 'success', __('hll.squads.squad_recon.title'));
        }
    }
};
