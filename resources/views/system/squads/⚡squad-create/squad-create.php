<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Traits\CheckAuthorizationSquadsTrait;
use Blockpc\Traits\AlertBrowserEvent;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;
    use CheckAuthorizationSquadsTrait;

    public Roster $roster;

    public string $name = '';

    public string $alias = '';

    public ?string $roster_type_squad = null;

    public int $pos_x = 0;

    public int $pos_y = 0;

    public int $z_index = 1;

    public function mount(): void
    {
        $this->checkAuthorization();
    }

    #[On('open-create-squad')]
    public function openModal(string $typeSquad): void
    {
        $this->roster_type_squad = $typeSquad;
        $this->modal('create-squad')->show();
    }

    /**
     * @return RosterTypeSquadEnum[]
     */
    #[Computed()]
    public function typeSquads(): array
    {
        return RosterTypeSquadEnum::cases();
    }

    public function save(): void
    {
        $this->checkAuthorization();

        $data = $this->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|max:255|unique:squads,alias',
            'roster_type_squad' => ['required', new Enum(RosterTypeSquadEnum::class)],
            'pos_x' => 'required|integer',
            'pos_y' => 'required|integer',
            'z_index' => 'required|integer',
        ]);

        if ($this->checkSquadsLimits()) {
            return;
        }

        $this->roster->squads()->create($data);

        $this->dispatch('add-squad', $this->roster_type_squad)->to('system::rosters.roster-template-manage');
        $this->cancelModal();

        $this->alert(
            __('hll.squads.create.message_success', ['name' => $data['name']]),
            type: 'success',
            title: __('hll.squads.create.title'),
        );
    }

    public function cancelModal(): void
    {
        $this->resetExcept('roster');
        $this->clearValidation();
        $this->modal('create-squad')->close();
    }

    private function checkSquadsLimits(): bool
    {
        $rosterTypeSquad = RosterTypeSquadEnum::from($this->roster_type_squad);
        $squadsCount = $this->roster->squads()->where('roster_type_squad', $this->roster_type_squad)->count();

        if ($squadsCount >= $rosterTypeSquad->capacity()) {
            $this->addError('squad_limit_reached',
                __('hll.squads.create.message_limit_reached', ['name' => $rosterTypeSquad->label(), 'max' => $rosterTypeSquad->capacity()]),
            );

            return true;
        }

        return false;
    }
};
