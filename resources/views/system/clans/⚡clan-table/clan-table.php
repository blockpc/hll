<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Models\Clan;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use PaginationTrait;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('clans.index'), 403, __('hll.clans.index.403'));
    }

    #[Computed()]
    public function clanes(): LengthAwarePaginator
    {
        return Clan::query()
            ->with('owner')
            ->latest()
            ->paginate($this->paginate);
    }

    public function confirmDelete(int $clanId): void
    {
        abort_unless(auth()->user()?->can('clans.delete'), 403, __('hll.clans.index.403'));

        $clan = Clan::query()->findOrFail($clanId);

        if ($clan->members()
            ->wherePivot('membership_role', '!=', ClanMembershipRoleEnum::Owner->value)
            ->exists()) {
            session()->flash('error', __('hll.clans.delete.error_has_members'));

            return;
        }

        $clan->delete();

        session()->flash('success', __('hll.clans.delete.success'));
    }
};
