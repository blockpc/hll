<?php

use App\Models\Roster;
use App\Models\Squad;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
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
        $squad = $this->roster->customSquads()->findOrFail($squadId);
        if ($squad->isFull()) {
            $this->alert(__('hll.squads.squad_custom.full_squad'), 'warning', __('hll.squads.squad_custom.title'));

            return;
        }
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
    public function remove_soldier(int $soldierId): void
    {
        $squad = $this->roster->customSquads()->whereHas('soldiers', function ($query) use ($soldierId) {
            $query->where('soldier_id', $soldierId);
        })->first();

        if ($squad) {
            $squad->soldiers()->detach($soldierId);
            $this->reRender();
        }
    }
};
