<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Clan;
use App\Models\Roster;
use App\Traits\CheckAuthorizationRostersTrait;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Lazy, Title('Plantilla Roster')] class extends Component
{
    use CheckAuthorizationRostersTrait;

    public Clan $clan;

    public Roster $roster;

    public ?string $searchSoldier = null;

    /** @var array<int, int> Soldier IDs selected for squad assignment */
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
     * Triggers component re-render when a squad is added.
     *
     * Listens to the 'add-squad' event and refreshes the component state.
     */
    #[On('add-squad')]
    public function squadAdded(): void {}
};
