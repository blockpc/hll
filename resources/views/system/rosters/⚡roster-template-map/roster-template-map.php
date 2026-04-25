<?php

use App\Models\Clan;
use App\Models\Roster;
use App\Traits\CheckAuthorizationRostersTrait;
use Livewire\Component;

new class extends Component
{
    use CheckAuthorizationRostersTrait;

    public Clan $clan;

    public Roster $roster;

    public ?string $searchSoldier = null;

    /** @var array<int, int> */
    public array $selectedSoldiers = [];

    public ?string $link = null;

    public function mount(): void
    {
        $this->checkAuthorization();

        $this->link = route('public.rosters.show', $this->roster->uuid);
    }
};
