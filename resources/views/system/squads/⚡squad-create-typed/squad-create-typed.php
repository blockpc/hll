<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Services\CreateRosterSquadService;
use App\Traits\CheckAuthorizationSquadsTrait;
use Blockpc\Traits\AlertBrowserEvent;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;
    use CheckAuthorizationSquadsTrait;

    public Roster $roster;

    public RosterTypeSquadEnum $type;

    public string $name = '';

    public string $alias = '';

    public bool $canCreate = false;

    public function mount(): void
    {
        $this->checkAuthorization();
        $this->canCreate = $this->squadService()->canCreate($this->roster, $this->type);
    }

    #[On('open-create-squad-typed')]
    public function openModal(string $typeSquad): void
    {
        $rosterTypeSquad = RosterTypeSquadEnum::tryFrom($typeSquad);

        if ($rosterTypeSquad === null || $rosterTypeSquad !== $this->type) {
            return;
        }

        if (! $this->checkCapacity()) {
            return;
        }

        $this->modal($this->modalName())->show();
    }

    public function save(): void
    {
        $this->checkAuthorization();

        /** @var array{name: string, alias: string} $data */
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|max:255|unique:squads,alias',
        ]);

        if (! $this->checkCapacity()) {
            return;
        }

        $this->squadService()->create(
            $this->roster,
            $data['name'],
            $data['alias'],
            $this->type,
        );

        $this->dispatch('add-squad', $this->type)->to('system::rosters.roster-template-manage');

        $this->canCreate = $this->squadService()->canCreate($this->roster, $this->type);

        $this->cancelModal();

        $this->alert(
            __('hll.squads.create.message_success', ['name' => $data['name']]),
            type: 'success',
            title: __($this->translationKeyPrefix().'.modal_create_title'),
        );
    }

    public function cancelModal(): void
    {
        $this->resetExcept('roster', 'type', 'canCreate');
        $this->clearValidation();
        $this->modal($this->modalName())->close();
    }

    public function title(): string
    {
        return __($this->translationKeyPrefix().'.title');
    }

    public function modalSubtitle(): string
    {
        return __($this->translationKeyPrefix().'.modal_subtitle');
    }

    public function modalName(): string
    {
        return 'create-squad-'.$this->type->value;
    }

    private function checkCapacity(): bool
    {
        $this->canCreate = $this->squadService()->canCreate($this->roster, $this->type);

        if (! $this->canCreate) {
            $this->alert(
                __($this->translationKeyPrefix().'.message_limit_squad'),
                type: 'warning',
                title: __($this->translationKeyPrefix().'.modal_create_title'),
            );

            return false;
        }

        return true;
    }

    private function translationKeyPrefix(): string
    {
        return match ($this->type) {
            RosterTypeSquadEnum::Infantry => 'hll.squads.squad_infantry',
            RosterTypeSquadEnum::Recon => 'hll.squads.squad_recon',
            RosterTypeSquadEnum::Armor => 'hll.squads.squad_armor',
            RosterTypeSquadEnum::Artillery => 'hll.squads.squad_artillery',
            default => 'hll.squads.create',
        };
    }

    private function squadService(): CreateRosterSquadService
    {
        return app(CreateRosterSquadService::class);
    }
};
