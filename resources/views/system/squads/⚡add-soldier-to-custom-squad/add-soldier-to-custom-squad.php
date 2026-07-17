<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Models\Squad;
use App\Services\AddSoldiersToSquadService;
use Blockpc\Traits\AlertBrowserEvent;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;

    public Roster $roster;

    public Squad $squad;

    public ?string $soldiersByName = null;

    #[On('open-add-soldier')]
    public function openModal(int $squadId): void
    {
        $squad = Squad::findOrFail($squadId);

        if ($squad->roster_type_squad !== RosterTypeSquadEnum::Custom) {
            return;
        }

        $this->squad = $squad;

        $this->modal('add-soldier-custom-squad')->show();
    }

    public function save()
    {
        $this->validate([
            'soldiersByName' => 'required|string',
        ], [], [
            'soldiersByName' => __('hll.squads.squad_custom.solidersByName'),
        ]);

        $added = $this->addSoldiersManually();

        if (! $added) {
            return;
        }

        $this->dispatch('add-soldiers', $this->squad->roster_type_squad->value)->to('system::rosters.roster-template-manage');
        $this->cancelModal();
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
        $this->modal('add-soldier-custom-squad')->close();
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

    private function extraValidationsSoldierByName(): ?string
    {
        if ($this->squad->soldiers()->where('display_name', $this->soldiersByName)->exists()) {
            return __('hll.squad_soldiers.soldier_already_assigned', ['name' => $this->soldiersByName]);
        }

        return null;
    }
};
