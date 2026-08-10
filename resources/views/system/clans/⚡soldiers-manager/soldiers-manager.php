<?php

use App\Enums\RoleSquadTypeEnum;
use App\Models\Clan;
use App\Models\Soldier;
use App\Services\AddSoldiersToClanService;
use App\Services\HellLetLooseApi;
use App\Traits\ExportImportSoldiersClanTrait;
use Blockpc\App\Rules\AreEqualsRule;
use Blockpc\Traits\AlertBrowserEvent;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;
    use PaginationTrait;
    use ExportImportSoldiersClanTrait;

    public Clan $clan;
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    public string $name = '';

    public bool $manySoldiers = false;

    public ?string $role = null;

    public ?string $observation = null;

    public ?string $bulkNames = null;

    #[Locked]
    public ?int $editingSoldierId = null;

    public ?string $soldier_name = null;

    public ?RoleSquadTypeEnum $soldier_role = null;

    public ?string $soldier_observation = null;

    public ?string $soldier_rcon = null;

    public int $soldier_level = 1;

    #[Locked]
    public ?int $deletingSoldierId = null;

    public ?string $currentNameToDelete = null;

    public ?string $current_name = null;

    public function mount(): void
    {
        $this->authorizeOwner();
    }

    #[Computed()]
    public function soldiers(): LengthAwarePaginator
    {
        return $this->clan->soldiers()
            ->search($this->search)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(12);
    }

    #[Computed]
    public function roleSquads(): array
    {
        return RoleSquadTypeEnum::cases();
    }

    public function save(AddSoldiersToClanService $addSoldiersService): void
    {
        $this->authorizeOwner();

        $addSoldiersService->for($this->clan);

        $data = $this->validate();

        $message = DB::transaction(function () use ($data, $addSoldiersService): string {
            if ($this->manySoldiers) {
                $result = $addSoldiersService->names($this->bulkNames)->saveBulk();
            } else {
                $role = ! empty($data['role']) ? RoleSquadTypeEnum::from($data['role']) : null;
                $result = $addSoldiersService->saveSingle($data['name'], $role, $data['observation'] ?? null);
            }

            return $addSoldiersService->messages($result);
        });

        $this->reset(['name', 'role', 'observation', 'bulkNames', 'manySoldiers']);

        $this->alert($message, title: __('hll.clans.soldiers.create.title'));

        $this->cancelModal('create-soldier-manager');
    }

    private function normalizeSoldierName(string $name): string
    {
        return Str::transliterate(trim($name));
    }

    private function authorizeOwner(): void
    {
        abort_unless(
            auth()->user()?->can('manageSoldiers', $this->clan),
            403,
            __('hll.clans.soldiers.create.403')
        );
    }

    public function cancel(): void
    {
        $this->cancelModal('create-soldier-manager');
    }

    protected function rules(): array
    {
        if ($this->manySoldiers) {
            return [
                'bulkNames' => ['required', 'string'],
            ];
        } else {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:32',
                    Rule::unique('soldiers', 'name')->where('clan_id', $this->clan->id),
                ],
                'role' => ['nullable', Rule::enum(RoleSquadTypeEnum::class)],
                'observation' => ['nullable', 'string', 'max:255'],
            ];
        }
    }

    public function showEditSoldier(int|string $soldierId): void
    {
        $this->authorizeOwner();

        $this->editingSoldierId = $soldierId;

        $soldier = $this->clan->soldiers()->findOrFail($soldierId);
        $this->soldier_name = $soldier->name;
        $this->soldier_role = $soldier->role;
        $this->soldier_observation = $soldier->observation;
        $this->soldier_rcon = $soldier->rcon;
        $this->soldier_level = $soldier->level;

        $this->modal('edit-soldier-manager')->show();
    }

    public function editSoldier(): void
    {
        $this->authorizeOwner();

        $this->soldier_name = $this->normalizeSoldierName($this->soldier_name ?? '');

        $this->validate([
            'editingSoldierId' => ['required', 'integer', Rule::exists('soldiers', 'id')->where('clan_id', $this->clan->id)],
            'soldier_name' => ['required', 'string', 'max:32', Rule::unique('soldiers', 'name')->where('clan_id', $this->clan->id)->ignore($this->editingSoldierId)],
            'soldier_role' => ['nullable', Rule::enum(RoleSquadTypeEnum::class)],
            'soldier_observation' => ['nullable', 'string', 'max:255'],
            'soldier_rcon' => ['nullable', 'string', 'max:255'],
            'soldier_level' => ['nullable', 'integer', 'min:1', 'max:500'],
        ], [
            'soldier_name.required' => __('hll.clans.soldiers.form.validations.name_required'),
            'soldier_name.unique' => __('hll.clans.soldiers.form.validations.name_unique'),
            'soldier_name.max' => __('hll.clans.soldiers.form.validations.name_max'),
            'soldier_role.enum' => __('hll.clans.soldiers.form.validations.role_enum'),
            'soldier_observation.max' => __('hll.clans.soldiers.form.validations.observation_max'),
            'soldier_rcon.max' => __('hll.clans.soldiers.form.validations.rcon_max'),
            'soldier_level.integer' => __('hll.clans.soldiers.form.validations.level_integer'),
            'soldier_level.min' => __('hll.clans.soldiers.form.validations.level_min'),
            'soldier_level.max' => __('hll.clans.soldiers.form.validations.level_max'),
        ]);

        $soldier = $this->clan->soldiers()->findOrFail($this->editingSoldierId);
        $soldier->update([
            'name' => $this->soldier_name,
            'role' => $this->soldier_role,
            'rcon' => $this->soldier_rcon,
            'level' => $this->soldier_level,
            'observation' => $this->soldier_observation,
        ]);

        $this->alert(__('hll.clans.soldiers.edit.message_success', ['name' => $soldier->name]), title: __('hll.clans.soldiers.edit.title'));

        $this->cancelModal('edit-soldier-manager');
    }

    public function cancelEditSoldier(): void
    {
        $this->cancelModal('edit-soldier-manager');
    }

    public function showDeleteSoldier(int|string $soldierId): void
    {
        $this->authorizeOwner();

        $soldier = $this->clan->soldiers()->findOrFail($soldierId);

        $this->deletingSoldierId = $soldier->id;
        $this->currentNameToDelete = $soldier->name;

        $this->modal('delete-soldier-manager')->show();
    }

    public function deleteSoldier(): void
    {
        $this->authorizeOwner();

        $this->validate([
            'deletingSoldierId' => ['required', 'integer', Rule::exists('soldiers', 'id')->where('clan_id', $this->clan->id)],
            'current_name' => ['required', 'string', (new AreEqualsRule($this->currentNameToDelete, __('hll.clans.soldiers.delete.current_name_error')))],
        ], [
            'current_name.required' => __('hll.clans.soldiers.delete.current_name_required'),
            'current_name.are_equals' => __('hll.clans.soldiers.delete.current_name_error'),
        ]);

        $soldier = $this->clan->soldiers()->findOrFail($this->deletingSoldierId);
        $soldier->delete();

        $this->alert(__('hll.clans.soldiers.delete.message_success', ['name' => $soldier->name]), title: __('hll.clans.soldiers.delete.title'));

        $this->cancelModal('delete-soldier-manager');
    }

    public function cancelDeleteSoldier(): void
    {
        $this->cancelModal('delete-soldier-manager');
    }

    public function cancelModal(string $modalName): void
    {
        $this->resetExcept('clan');
        $this->clearValidation();
        $this->modal($modalName)->close();
    }

    public function updatedImportFile($file): void
    {
        $this->resetValidation('importFile');
    }

    public function sort(string $column = 'name'): void {
        if (! in_array($column, ['name', 'level'], true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public array $playerProfile = [];

    public function getPlayerProfile(HellLetLooseApi $api, int $id): void
    {
        $soldier = $this->clan->soldiers()->find($id);

        if (! $soldier) {
            $this->alert(__('hll.clans.soldiers.api.player_not_found'), title: __('hll.clans.soldiers.api.soldier_not_found_title'));
            return;
        }

        if (! $soldier->rcon) {
            $this->alert(__('hll.clans.soldiers.api.player_no_rcon'), title: __('hll.clans.soldiers.api.player_no_rcon_title'));
            return;
        }

        try {
            $playerProfile = $api->getPlayerProfile($soldier->rcon);
        } catch (RequestException|ConnectionException $exception) {
            $this->alert(__('hll.clans.soldiers.api.player_not_found'), title: __('hll.clans.soldiers.api.player_not_found_title'));
            return;
        }

        $this->playerProfile = $playerProfile;

        if (! isset($this->playerProfile['result']['soldier'])) {
            $this->alert(__('hll.clans.soldiers.api.player_not_found'), title: __('hll.clans.soldiers.api.player_not_found_title'));
            return;
        }

        $soldierData = $this->playerProfile['result']['soldier'];
        $soldier_level = $soldierData['level'] ?? 1;

        $soldier->update([
            'level' => $soldier_level,
        ]);

        $this->dispatch('refresh-soldiers-manager');
    }

    #[On('refresh-soldiers-manager')]
    public function rerender(): void {}
};
