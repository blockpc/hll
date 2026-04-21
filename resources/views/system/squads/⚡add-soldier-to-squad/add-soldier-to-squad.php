<?php

use App\Models\Roster;
use App\Models\Soldier;
use App\Models\Squad;
use App\Services\AddSoldiersToSquadService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public Squad $squad;

    /**
     * True for selection by ID, false for manual entry by name.
     */
    public bool $singleSelection = false;

    public ?string $searchSoldier = null;

    // public ?int $soldierId = null;

    public array $selectedSoldiers = [];

    public ?string $soldiersByName = null;

    public bool $squadFull = false;

    /** @var int[] */
    public array $soldiersFromClanIds = [];

    /** @var int[] */
    public array $soldiersAddedRoster = [];

    public function mount(): void
    {
        $this->roster->loadMissing('clan.soldiers');
    }

    #[Computed()]
    public function soldiers(): Collection
    {
        return $this->roster->clan
            ->soldiers()
            ->search($this->searchSoldier)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    #[On('open-add-soldier')]
    public function openModal(int $squadId): void
    {
        $this->squad = Squad::findOrFail($squadId);
        $this->squadFull = $this->squad->isFull();
        $this->soldiersFromClanIds = $this->roster->clan->soldiers->pluck('id')->toArray();
        $this->soldiersAddedRoster = $this->roster->soldiersFromClan()->keys()->toArray();

        if ($checkCapacitiesError = $this->chekCapatities()) {
            $this->dispatch('show', $checkCapacitiesError, 'warning', __('hll.squad_soldiers.add.title'))->to('alert');

            return;
        }

        $this->modal('add-soldier')->show();
    }

    public function setSoldierId(int $soldierId): void
    {
        if ( ! in_array($soldierId, $this->soldiersFromClanIds, true) ) {
            $this->addError('soldierId', __('hll.squad_soldiers.soldier_not_in_clan_from_roster'));

            return;
        }

        $soldier = Soldier::find($soldierId);

        if ($this->squad->soldiers()->where('soldier_id', $soldierId)->exists()) {
            $this->addError('soldierId', __('hll.squad_soldiers.soldier_already_assigned', ['name' => $soldier ? $soldier->name : 'ID ' . $soldierId]));

            return;
        }

        if (!in_array($soldierId, $this->selectedSoldiers, true)) {
            $this->selectedSoldiers[] = $soldierId;
        } else {
            $this->selectedSoldiers = array_values(
                array_filter($this->selectedSoldiers, fn($id) => $id !== $soldierId)
            );
        }

        $this->soldiersByName = null;
    }

    public function updatedSingleSelection(bool $value): void
    {
        if (! $value) {
            $this->selectedSoldiers = [];
        } else {
            $this->soldiersByName = null;
        }
    }

    public function addSoldier(): void
    {
        $this->validate([
            'singleSelection' => 'required|boolean',
            'selectedSoldiers' => 'exclude_unless:singleSelection,true|array|min:1',
            'selectedSoldiers.*' => 'exists:soldiers,id',
            'soldiersByName' => 'exclude_unless:singleSelection,false|required|string',
        ], [], [
            'selectedSoldiers' => __('hll.squad_soldiers.add.form.soldier_by_ids'),
            'soldiersByName' => __('hll.squad_soldiers.add.form.soldier_by_name'),
        ]);

        $added = $this->singleSelection
            ? $this->addSoldierByIds()
            : $this->addSoldiersManually();

        if (! $added) {
            return;
        }

        $this->dispatch('add-soldiers', $this->squad->roster_type_squad->value)->to('system::rosters.roster-template-manage');
        $this->cancelModal();
    }

    public function addSoldierByIds(): bool
    {
        $initialSquadCount = $this->squad->soldiers()->count();
        $soldiersCount = $initialSquadCount;

        // Primero validar todos los soldados antes de agregar ninguno
        foreach ($this->selectedSoldiers as $soldierId) {
            $soldier = Soldier::findOrFail($soldierId);

            if ($validationError = $this->extraValidationsSoldierId($soldier)) {
                $this->addError('soldierId', $validationError);

                return false;
            }

            // Verificar capacidad considerando los soldados que se van a agregar
            $soldiersCount++;
            if ($soldiersCount > $this->squad->capacity) {
                $this->addError('soldierId', __('hll.squad_soldiers.squad_full'));

                return false;
            }

            if ($this->roster->assignedSoldiersCount() + ($soldiersCount - $initialSquadCount) > $this->roster->max_soldiers) {
                $this->addError('soldierId', __('hll.squad_soldiers.roster_full'));

                return false;
            }
        }

        // Si todas las validaciones pasaron, agregar todos los soldados
        foreach ($this->selectedSoldiers as $soldierId) {
            if (! $this->addSoldierById($soldierId)) {
                return false;
            }
        }

        return true;
    }

    public function addSoldierById(int $soldierId): bool
    {
        $soldier = Soldier::findOrFail($soldierId);

        $error = null;

        DB::transaction(function () use ($soldier, &$error) {
            $this->squad = Squad::lockForUpdate()->findOrFail($this->squad->id);

            if ($validationError = $this->extraValidationsSoldierId($soldier)) {
                $error = $validationError;

                return;
            }

            $nextSlotNumber = $this->squad->soldiers()->count() + 1;

            $this->squad->soldiers()->create([
                'soldier_id' => $soldier->id,
                'slot_number' => $nextSlotNumber,
                'display_name' => $soldier->name,
            ]);
        });

        if ($error) {
            $this->addError('soldierId', $error);

            return false;
        }

        return true;
    }

    public function addSoldiersManually(): bool
    {
        $error = null;

        DB::transaction(function () use (&$error) {
            $this->squad = Squad::lockForUpdate()->findOrFail($this->squad->id);
            $this->roster = Roster::lockForUpdate()->findOrFail($this->roster->id);

            if ($validationError = $this->extraValidationsSoldierByName()) {
                $error = $validationError;

                return;
            }

            $service = new AddSoldiersToSquadService;
            $result = $service
                ->for($this->squad)
                ->names((string) $this->soldiersByName)
                ->saveBulk();

            if (! empty($result['skippedSquadFull'])) {
                $error = __('hll.squad_soldiers.squad_full');

                return;
            }

            if (! empty($result['skippedRosterFull'])) {
                $error = __('hll.squad_soldiers.roster_full');

                return;
            }

            if ($result['created'] === 0 && ! empty($result['duplicatesIgnored'])) {
                $error = __('hll.squad_soldiers.soldier_already_assigned', ['name' => $result['duplicatesIgnored'][0]]);
            }
        });

        if ($error) {
            $this->addError('soldiersByName', $error);

            return false;
        }

        return true;
    }

    public function cancelModal(): void
    {
        $this->resetExcept('roster');
        $this->clearValidation();
        $this->modal('add-soldier')->close();
    }

    private function extraValidationsSoldierId(?Soldier $soldier): ?string
    {
        if (! $soldier) {
            return __('hll.squad_soldiers.soldier_not_found');
        }

        if ($soldier->clan_id !== $this->squad->roster->clan_id) {
            return __('hll.squad_soldiers.soldier_not_in_clan_from_roster');
        }

        if ($soldier->squads()->where('roster_id', $this->squad->roster_id)->exists()) {
            return __('hll.squad_soldiers.soldier_already_assigned', ['name' => $soldier->name]);
        }

        if ($validationError = $this->chekCapatities()) {
            return $validationError;
        }

        return null;
    }

    private function extraValidationsSoldierByName(): ?string
    {
        if ($this->squad->soldiers()->where('display_name', $this->soldiersByName)->exists()) {
            return __('hll.squad_soldiers.soldier_already_assigned', ['name' => $this->soldiersByName]);
        }

        if ($validationError = $this->chekCapatities()) {
            return $validationError;
        }

        return null;
    }

    public function cancelDeleteSquad(): void
    {
        $this->clearValidation();
        $this->modal("delete-squad-{$this->squad->id}")->close();
    }

    public function deleteSquad(int $squadId): void
    {
        if (! $this->squad || $this->squad->id !== $squadId) {
            $this->addError('squad', __('hll.squads.delete.message_error'));

            return;
        }

        $squad = $this->roster->squads()->findOrFail($squadId);

        if (! $squad) {
            $this->addError('squad', __('hll.squads.delete.message_error'));

            return;
        }

        $squadType = $squad->roster_type_squad;

        $squad->delete();

        $message = __('hll.squads.delete.message_success', ['name' => $squad->name]);

        $this->dispatch('show', $message, 'warning', __('hll.squads.delete.title'))->to('alert');

        $this->clearValidation();
        $this->modal("delete-squad-{$squadId}")->close();
        $this->cancelModal();

        $this->dispatch('delete-squad', $squadType)->to('system::rosters.roster-template-manage');
    }

    private function chekCapatities(): ?string
    {
        if ($this->squad->soldiers()->count() >= $this->squad->capacity) {
            return __('hll.squad_soldiers.squad_full');
        }

        if ($this->roster->assignedSoldiersCount() >= $this->roster->max_soldiers) {
            return __('hll.squad_soldiers.roster_full');
        }

        return null;
    }
};
