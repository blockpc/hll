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

    public bool $singleSelection = false;

    public ?string $searchSoldier = null;

    public ?int $soldierId = null;

    public ?string $soldiersByName = null;

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
        $this->modal('add-soldier')->show();
    }

    public function setSoldierId(int $soldierId): void
    {
        $this->soldierId = $soldierId;
        $this->soldiersByName = null;
    }

    public function updatedManySoldiers(bool $value): void
    {
        if (! $value) {
            $this->soldierId = null;
        } else {
            $this->soldiersByName = null;
        }
    }

    public function addSoldier(): void
    {
        $this->validate([
            'soldierId' => 'nullable|exists:soldiers,id',
            'soldiersByName' => 'required_without:soldierId|nullable|string',
        ], [], [
            'soldierId' => __('hll.squad_soldiers.add.form.soldier_by_id'),
            'soldiersByName' => __('hll.squad_soldiers.add.form.soldier_by_name'),
        ]);

        $added = $this->soldierId
            ? $this->addSoldierById()
            : $this->addSoldiersManually();

        if (! $added) {
            return;
        }

        $this->dispatch('add-soldiers', $this->squad->roster_type_squad->value)->to('system::rosters.roster-template-manage');
        $this->cancelModal();
    }

    public function addSoldierById(): bool
    {
        $soldier = Soldier::findOrFail($this->soldierId);

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

            if (! empty($result['skippedFull'])) {
                $error = __('hll.squad_soldiers.squad_full');
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

        if ($this->squad->soldiers()->count() >= $this->squad->capacity) {
            return __('hll.squad_soldiers.squad_full');
        }

        return null;
    }

    private function extraValidationsSoldierByName(): ?string
    {
        if ($this->squad->soldiers()->where('display_name', $this->soldiersByName)->exists()) {
            return __('hll.squad_soldiers.soldier_already_assigned', ['name' => $this->soldiersByName]);
        }

        if ($this->squad->soldiers()->count() >= $this->squad->capacity) {
            return __('hll.squad_soldiers.squad_full');
        }

        return null;
    }
};
