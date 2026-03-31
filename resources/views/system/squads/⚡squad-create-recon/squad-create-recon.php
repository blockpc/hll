<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Models\Squad;
use App\Traits\CheckAuthorizationSquadsTrait;
use Blockpc\Traits\AlertBrowserEvent;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;
    use CheckAuthorizationSquadsTrait;

    public Roster $roster;

    public string $name = '';

    public string $alias = '';

    public ?RosterTypeSquadEnum $roster_type_squad = null;

    public $reconSquads = null;

    public bool $canCreate = false;

    public function mount(): void
    {
        $this->checkAuthorization();

        $this->roster_type_squad = RosterTypeSquadEnum::Recon;
        $this->reconSquads = $this->roster->reconSquads()->get();
        $this->canCreate = $this->reconSquads->count() < RosterTypeSquadEnum::Recon->capacity();
    }

    #[On('open-create-squad-recon')]
    public function openModal(string $typeSquad): void
    {
        $this->roster_type_squad = RosterTypeSquadEnum::tryFrom($typeSquad);
        if ($this->checkCapacity()) {
            $this->modal('create-squad-recon')->show();
        }
    }

    public function save(): void
    {
        $this->checkAuthorization();

        $data = $this->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|max:255|unique:squads,alias',
        ]);

        Squad::create([
            'roster_id' => $this->roster->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'roster_type_squad' => $this->roster_type_squad,
        ]);

        $this->dispatch('add-squad', $this->roster_type_squad)->to('system::rosters.roster-template-manage');

        // Refresh reconSquads before checking capacity
        $this->reconSquads = $this->roster->reconSquads()->get();
        $this->canCreate = $this->reconSquads->count() < RosterTypeSquadEnum::Recon->capacity();

        $this->cancelModal();

        $this->alert(
            __('hll.squads.create.message_success', ['name' => $data['name']]),
            type: 'success',
            title: __('hll.squads.squad_recon.modal_create_title'),
        );
    }

    public function cancelModal(): void
    {
        $this->resetExcept('roster', 'roster_type_squad', 'canCreate');
        $this->clearValidation();
        $this->modal('create-squad-recon')->close();
    }

    private function checkCapacity(): bool
    {
        $this->reconSquads = $this->roster->reconSquads()->get();
        $this->canCreate = $this->reconSquads->count() < RosterTypeSquadEnum::Recon->capacity();
        if (! $this->canCreate) {
            $this->alert(
                __('hll.squads.squad_recon.message_limit_squad'),
                type: 'warning',
                title: __('hll.squads.squad_recon.modal_create_title'),
            );

            return false;
        }

        return true;
    }
};
