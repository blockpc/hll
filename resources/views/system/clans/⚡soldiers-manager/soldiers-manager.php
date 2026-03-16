<?php

use App\Enums\RoleSquadTypeEnum;
use App\Models\Clan;
use Blockpc\App\Rules\AreEqualsRule;
use Blockpc\Traits\AlertBrowserEvent;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;
    use PaginationTrait;

    public Clan $clan;

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
        return $this->clan->soldiers()->orderBy('name')->paginate(12);
    }

    #[Computed]
    public function roleSquads(): array
    {
        return RoleSquadTypeEnum::cases();
    }

    public function save(): void
    {
        $this->authorizeOwner();

        if (! $this->manySoldiers) {
            $this->name = $this->normalizeSoldierName($this->name);
        }

        $data = $this->validate();

        $message = DB::transaction(function () use ($data): string {
            if ($this->manySoldiers) {
                $createds = $this->saveBulk();
                $this->reset('bulkNames', 'manySoldiers');

                return __('hll.clans.soldiers.create.bulk_message_success', ['count' => $createds]);
            } else {
                $this->clan->soldiers()->create($data);
                $this->reset(['name', 'role', 'observation', 'manySoldiers']);

                return __('hll.clans.soldiers.create.message_success', ['name' => $data['name'] ?? '']);
            }
        });

        $this->alert($message, title: __('hll.clans.soldiers.create.title'));

        $this->cancelModal('create-soldier-manager');
    }

    public function saveBulk(): int
    {
        $names = collect(preg_split('/[\n,]+/', $this->bulkNames))
            ->map(fn (string $name) => $this->normalizeSoldierName($name))
            ->filter(fn (string $name) => $name !== '')
            ->filter(fn (string $name) => mb_strlen($name) <= 32)
            ->unique()
            ->values();

        $created = 0;
        foreach ($names as $name) {
            $soldier = $this->clan->soldiers()->firstOrCreate(['name' => $name]);
            if ($soldier->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    private function normalizeSoldierName(string $name): string
    {
        return Str::transliterate(Str::lower(trim($name)));
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

    public function showEditSoldier(int $soldierId): void
    {
        $this->authorizeOwner();

        $this->editingSoldierId = $soldierId;

        $soldier = $this->clan->soldiers()->findOrFail($soldierId);
        $this->soldier_name = $soldier->name;
        $this->soldier_role = $soldier->role;
        $this->soldier_observation = $soldier->observation;

        $this->modal('edit-soldier-manager')->show();
    }

    public function editSoldier(): void
    {
        $this->authorizeOwner();

        $this->soldier_name = $this->normalizeSoldierName($this->soldier_name);

        $this->validate([
            'editingSoldierId' => ['required', 'integer', Rule::exists('soldiers', 'id')->where('clan_id', $this->clan->id)],
            'soldier_name' => ['required', 'string', 'max:32', Rule::unique('soldiers', 'name')->where('clan_id', $this->clan->id)->ignore($this->editingSoldierId)],
            'soldier_role' => ['nullable', Rule::enum(RoleSquadTypeEnum::class)],
            'soldier_observation' => ['nullable', 'string', 'max:255'],
        ]);

        $soldier = $this->clan->soldiers()->findOrFail($this->editingSoldierId);
        $soldier->update([
            'name' => $this->soldier_name,
            'role' => $this->soldier_role,
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
};
