<?php

use App\Models\Clan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public Clan $clan;

    #[Computed]
    public function members(): Collection
    {
        return $this->clan->members;
    }

    #[Computed]
    public function soldiers(): LengthAwarePaginator
    {
        return $this->clan->soldiers()->paginate(perPage: 12, pageName: 'soldiers_page');
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{id: int, name: string}>
     */
    #[Computed]
    public function rosters(): \Illuminate\Support\Collection
    {
        return collect([]);
    }
};
