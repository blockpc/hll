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

    public array $selectedSoldiers = [];

    public function mount(): void
    {
        $this->checkAuthorization();
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
        $this->dispatch('open-create-squad', $typeSquad);
    }

    /**
     * Re-renders the appropriate squad component based on type.
     *
     * Listens to: `add-soldiers`, `add-squad`
     */
    #[On('add-soldiers')]
    #[On('add-squad')]
    public function dispatchToTypeSquad(?RosterTypeSquadEnum $squadType = null): void
    {
        match ($squadType) {
            RosterTypeSquadEnum::Custom => $this->dispatch('re-render')->to('system::squads.squad-custom'),
            RosterTypeSquadEnum::Commander => $this->dispatch('re-render')->to('system::squads.squad-commander'),
            default => null,
        };
    }
};
