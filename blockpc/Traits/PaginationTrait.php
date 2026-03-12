<?php

declare(strict_types=1);

namespace Blockpc\Traits;

use Livewire\WithPagination;

trait PaginationTrait
{
    use WithPagination;

    public string $search = '';

    public int $paginate = 10;

    public bool $softDeletes = false;

    public function cleanSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updatingSoftDeletes(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
