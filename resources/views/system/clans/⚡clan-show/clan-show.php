<?php

use App\Models\Clan;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public Clan $clan;

    #[Computed]
    public function members(): LengthAwarePaginator
    {
        return $this->clan->members()->orderBy('name')->paginate(perPage: 12, pageName: 'members_page');
    }

    #[Computed]
    public function soldiers(): LengthAwarePaginator
    {
        return $this->clan->soldiers()->orderBy('name')->paginate(perPage: 12, pageName: 'soldiers_page');
    }

    #[Computed]
    public function rosters(): LengthAwarePaginator
    {
        return $this->clan->rosters()->latest()->paginate(perPage: 12, pageName: 'rosters_page');
    }
};
