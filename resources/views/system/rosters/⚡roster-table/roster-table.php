<?php

use App\Models\Clan;
use App\Models\Roster;
use Blockpc\App\Rules\AreEqualsRule;
use Blockpc\Traits\AlertBrowserEvent;
use Blockpc\Traits\AuthorizesRoleOrPermissionTrait;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Listado de Rosters')] class extends Component
{
    use AuthorizesRoleOrPermissionTrait;
    use AlertBrowserEvent;
    use PaginationTrait;

    public ?Clan $clan = null;

    public bool $isClanFilterApplied = false;

    private ?array $cachedAuthorizedClanIds = null;

    #[Locked]
    public ?int $deletingRosterId = null;

    public ?string $currentNameToDelete = null;

    public ?string $current_name = null;

    public function mount(?Clan $clan = null): void
    {
        $this->checkAuthorization();

        if ($clan) {
            abort_unless(
                $this->canViewClan($clan),
                403,
                __('hll.clans.rosters.403')
            );

            $this->clan = $clan;

            $this->isClanFilterApplied = true;

            return;
        }

        $this->clan = $this->resolveDefaultClan();
    }

    #[Computed]
    public function rosters(): LengthAwarePaginator
    {
        $query = Roster::query();

        if (! $this->isGlobalAdmin()) {
            $query->whereIn('clan_id', $this->authorizedClanIds());
        }

        if ($this->isClanFilterApplied && $this->clan?->exists) {
            $query->where('clan_id', $this->clan->id);
        }

        return $query
            ->with(['map', 'centralPoint'])
            ->search($this->search)
            ->latest()
            ->paginate($this->paginate);
    }

    private function checkAuthorization(): void
    {
        abort_unless(
            $this->authorizeRoleOrPermission([
                'sudo',
                'clan_owner',
                'clan_helper',
                'super admin',
            ]),
            403,
            __('hll.clans.rosters.403')
        );
    }

    private function canViewClan(Clan $clan): bool
    {
        if ($this->isGlobalAdmin()) {
            return true;
        }

        return in_array($clan->id, $this->authorizedClanIds(), true);
    }

    private function isGlobalAdmin(): bool
    {
        $user = auth()->user();

        return $user?->hasRole('sudo') || $user?->hasPermissionTo('super admin');
    }

    /**
     * @return array<int, int>
     */
    private function authorizedClanIds(): array
    {
        if ($this->cachedAuthorizedClanIds !== null) {
            return $this->cachedAuthorizedClanIds;
        }

        $user = auth()->user();

        if (! $user) {
            return $this->cachedAuthorizedClanIds = [];
        }

        $clanIds = $user->clans()->pluck('clans.id')->map(fn ($id) => (int) $id)->all();

        if ($user->ownedClan?->id) {
            $clanIds[] = (int) $user->ownedClan->id;
        }

        return $this->cachedAuthorizedClanIds = array_values(array_unique($clanIds));
    }

    private function resolveDefaultClan(): Clan
    {
        if ($this->isGlobalAdmin()) {
            return Clan::query()->first() ?? new Clan();
        }

        $authorizedClanIds = $this->authorizedClanIds();

        abort_if(
            empty($authorizedClanIds),
            403,
            __('hll.clans.rosters.403')
        );

        return Clan::query()
            ->whereIn('id', $authorizedClanIds)
            ->orderBy('id')
            ->firstOrFail();
    }

    public function showDeleteRoster(int|string $rosterId): void
    {
        $roster = Roster::query()->findOrFail($rosterId);

        $this->deletingRosterId = $roster->id;
        $this->currentNameToDelete = $roster->name;

        $this->authorizeOwner($roster);

        $this->modal('delete-roster-manager')->show();
    }

    public function deleteRoster(): void
    {
        $roster = Roster::query()->findOrFail($this->deletingRosterId);

        $this->authorizeOwner($roster);

        $this->validate([
             'current_name' => ['required', 'string', (new AreEqualsRule($this->currentNameToDelete, __('hll.clans.rosters.delete.current_name_error')))],
        ], [], [
            'current_name' => __('hll.clans.rosters.delete.current_name'),
        ]);

        $type = 'success';
        $message = '';
        $imagePath = $roster->image;
        try {
            DB::transaction(function () use ($roster): void {
                $roster->delete();
            });

            $message = __('hll.clans.rosters.delete.message_success', ['name' => $this->currentNameToDelete]);
        } catch(\Throwable $th) {
            Log::error("Error al eliminar un roster. {$th->getMessage()} | {$th->getFile()} | {$th->getLine()}");
            $type = 'error';
            $message = __('hll.clans.rosters.delete.error_transaction');
        }

        if ($type === 'success' && $imagePath) {
            try {
                Storage::disk('public')->delete($imagePath);
            } catch (\Throwable $th) {
                Log::warning("Roster eliminado, pero no se pudo borrar su imagen. {$th->getMessage()} | {$th->getFile()} | {$th->getLine()}");
            }
        }

        $this->alert($message, $type, title: __('hll.clans.rosters.delete.title'));
        $this->cancelModal('delete-roster-manager');
    }

    private function authorizeOwner(Roster $roster): void
    {
        abort_unless(
            auth()->user()?->can('delete', $roster),
            403,
            __('hll.clans.rosters.delete.403')
        );
    }

    public function cancelDeleteRoster(): void
    {
        $this->cancelModal('delete-roster-manager');
    }

    public function cancelModal(string $modalName): void
    {
        $this->reset(['deletingRosterId', 'currentNameToDelete', 'current_name']);
        $this->clearValidation();
        $this->modal($modalName)->close();
    }
};
