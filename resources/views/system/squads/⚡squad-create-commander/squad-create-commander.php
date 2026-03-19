<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Models\Squad;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public bool $commandSquads = false;

    public string $name = '';

    public string $alias = '';

    public ?int $selectedSoldierId = null;

    public Collection $soldiers;

    public function mount(Roster $roster, Collection $soldiers): void
    {
        $this->roster = $roster;
        $this->commandSquads = $roster->commandSquads()->exists();

        $this->soldiers = $soldiers;
    }

    public function addCommander(int $soldierId): void
    {
        $this->selectedSoldierId = $soldierId;
    }

    /**
     * Validate and create the commander squad.
     * Updates commandSquads state and closes the modal on success.
     */
    public function save(): void
    {
        $this->validate([
            'selectedSoldierId' => 'required|exists:soldiers,id',
            'name' => 'required|string|max:32',
            'alias' => 'nullable|string|max:8',
        ], [], [
            'selectedSoldierId' => __('hll.squads.form.soldier'),
            'name' => __('hll.squads.form.name'),
            'alias' => __('hll.squads.form.alias'),
        ]);

        Squad::create([
            'roster_id' => $this->roster->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'roster_type_squad' => RosterTypeSquadEnum::Command,
        ]);

        $this->commandSquads = true;

        $this->cancelModal();
    }

    public function cancelModal(): void
    {
        $this->reset('name', 'alias');
        $this->clearValidation();
        $this->modal('create-squad-command')->close();
    }
};
