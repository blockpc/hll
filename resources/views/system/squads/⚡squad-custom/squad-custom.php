<?php

use App\Models\Roster;
use App\Models\SquadSoldier;
use Blockpc\Traits\AlertBrowserEvent;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;

    public Roster $roster;

    public bool $buttons = false;

    /** @var Collection<int, \App\Models\Squad>|null */
    public ?Collection $customSquads = null;

    public int $countSquads = 0;

    public function mount(Roster $roster): void
    {
        $this->roster = $roster->loadMissing('customSquads');
        $this->customSquads = $this->roster->customSquads;
        $this->countSquads = $this->customSquads->count();
    }

    /**
     * Opens the add soldier modal for the specified squad.
     * Shows a warning alert and returns early if the squad is full.
     */
    public function addSoldier(int $squadId): void
    {
        $this->dispatch('open-add-soldier', $squadId);
    }

    #[On('re-render')]
    public function reRender(): void
    {
        $this->roster->refresh();
        $this->customSquads = $this->roster->customSquads;
        $this->countSquads = $this->customSquads->count();
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
        $this->alert(__('hll.squads.soldier_removed', ['name' => $name]), 'success', __('hll.squads.squad_custom.title'));
    }
};
