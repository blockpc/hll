<?php

use App\Models\Roster;
use App\Models\Soldier;
use App\Models\Squad;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public Squad $squad;

    public bool $option = false;

    public ?string $searchSoldier = null;

    public ?int $soldierId = null;

    public ?string $soldierByName = null;

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
        $this->soldierByName = null;
    }

    public function updatedOption(bool $value): void
    {
        if (! $value) {
            $this->soldierId = null;
        } else {
            $this->soldierByName = null;
        }
    }

    public function addSoldier(): void
    {
        $this->validate([
            'soldierId' => 'nullable|exists:soldiers,id',
            'soldierByName' => 'required_without:soldierId|nullable|string|max:32',
        ]);

        if ($this->soldierId) {
            $this->addSoldierById();
        } else {
            $this->addSoldierManually();
        }
    }

    public function addSoldierById(): void
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

            $this->squad->soldiers()->attach($soldier->id, [
                'slot_number' => $nextSlotNumber,
                'display_name' => $soldier->name,
            ]);
        });

        if ($error) {
            $this->addError('soldierId', $error);
        }
    }

    public function addSoldierManually(): void
    {
        $error = null;

        DB::transaction(function () use (&$error) {
            $this->squad = Squad::lockForUpdate()->findOrFail($this->squad->id);

            if ($validationError = $this->extraValidationsSoldierByName()) {
                $error = $validationError;

                return;
            }

            $nextSlotNumber = $this->squad->soldiers()->count() + 1;

            $this->squad->soldiers()->create([
                'display_name' => $this->soldierByName,
                'slot_number' => $nextSlotNumber,
            ]);
        });

        if ($error) {
            $this->addError('soldierByName', $error);
        }
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
        if ($this->squad->soldiers()->where('display_name', $this->soldierByName)->exists()) {
            return __('hll.squad_soldiers.soldier_already_assigned', ['name' => $this->soldierByName]);
        }

        if ($this->squad->soldiers()->count() >= $this->squad->capacity) {
            return __('hll.squad_soldiers.squad_full');
        }

        return null;
    }
};
