<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Clan;
use App\Models\Roster;
use App\Traits\CheckAuthorizationRostersTrait;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use CheckAuthorizationRostersTrait;

    public Clan $clan;

    public Roster $roster;

    public ?string $searchSoldier = null;

    /**
     * @var array<int, int>
     */
    public array $selectedSoldiers = [];

    public function mount(): void
    {
        $this->checkAuthorization();

        $this->selectedSoldiers = $this->roster->soldiersFromClan()->keys()->toArray();
    }

    /**
     * @return array<RosterTypeSquadEnum>
     */
    #[Computed]
    public function typeSquads(): array
    {
        return RosterTypeSquadEnum::cases();
    }

    /**
     * @return Collection<int, string>
     */
    #[Computed]
    public function soldiers(): Collection
    {
        return $this->clan->soldiers()
            ->search($this->searchSoldier)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function createSquad(string $typeSquad): void
    {
        $rosterTypeSquad = RosterTypeSquadEnum::tryFrom($typeSquad);

        if (in_array($rosterTypeSquad, [
            RosterTypeSquadEnum::Infantry,
            RosterTypeSquadEnum::Recon,
            RosterTypeSquadEnum::Armor,
            RosterTypeSquadEnum::Artillery,
        ], true)) {
            $this->dispatch('open-create-squad-typed', $typeSquad);

            return;
        }

        $this->dispatch('open-create-squad', $typeSquad);
    }

    /**
     * Re-renders the appropriate squad component based on type.
     *
     * Listens to: `add-soldiers`, `add-squad`, and `delete-squad` events, which are emitted after adding soldiers, adding a squad, or deleting a squad respectively. The method checks the type of squad that was modified and dispatches a `re-render` event to the corresponding squad component to refresh its data.
     */
    #[On('delete-squad')]
    #[On('add-soldiers')]
    #[On('add-squad')]
    public function dispatchToTypeSquad(?RosterTypeSquadEnum $squadType = null): void
    {
        match ($squadType) {
            RosterTypeSquadEnum::Custom => $this->dispatch('re-render')->to('system::squads.squad-custom'),
            RosterTypeSquadEnum::Commander => $this->dispatch('re-render')->to('system::squads.squad-commander'),
            RosterTypeSquadEnum::Recon => $this->dispatch('re-render')->to('system::squads.squad-recon'),
            RosterTypeSquadEnum::Infantry => $this->dispatch('re-render')->to('system::squads.squad-infantry'),
            RosterTypeSquadEnum::Artillery => $this->dispatch('re-render')->to('system::squads.squad-artillery'),
            RosterTypeSquadEnum::Armor => $this->dispatch('re-render')->to('system::squads.squad-armor'),
            default => null,
        };

        $this->selectedSoldiers = $this->roster->soldiersFromClan()->keys()->toArray();
    }
};
