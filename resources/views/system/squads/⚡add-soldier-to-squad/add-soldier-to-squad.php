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

    public bool $squadFull = false;

    /** @var int[] */
    public array $soldiersFromClanIds = [];

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
        $this->soldiersFromClanIds = $this->roster->soldiersFromClan()->keys()->toArray();

        if ($checkCapacitiesError = $this->chekCapatities()) {
            $this->dispatch('show', $checkCapacitiesError, 'warning', __('hll.squad_soldiers.add.title'))->to('alert');

            return;
        }

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
