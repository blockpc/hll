<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Clan;
use App\Models\Roster;
use App\Traits\CheckAuthorizationRostersTrait;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use CheckAuthorizationRostersTrait;

    public Clan $clan;

    public Roster $roster;

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
};
