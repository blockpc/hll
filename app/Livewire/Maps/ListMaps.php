<?php

declare(strict_types=1);

namespace App\Livewire\Maps;

use App\Models\Map;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

final class ListMaps extends Component
{
    use PaginationTrait;

    public function mount(): void
    {
        $this->paginate = 12;
    }

    #[Layout('layouts.app')]
    #[Title('Notas')]
    public function render(): View
    {
        return view('livewire.maps.list-maps');
    }

    #[Computed()]
    public function maps(): LengthAwarePaginator
    {
        return Map::query()
            ->search($this->search)
            ->orderBy('name')
            ->paginate($this->paginate);
    }
}
