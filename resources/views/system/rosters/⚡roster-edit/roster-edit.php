<?php

use App\Enums\FactionTypeEnum;
use App\Models\CentralPoint;
use App\Models\Clan;
use App\Models\Map;
use App\Models\Roster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Editar Roster')] class extends Component
{
    use WithFileUploads;

    public Clan $clan;

    public Roster $roster;

    public string $name = '';

    public ?TemporaryUploadedFile $image = null;

    public ?string $description = null;

    public int|string|null $map_id = null;

    public int|string|null $central_point_id = null;

    public string|FactionTypeEnum|null $faction = null;

    public bool $is_public = false;

    public bool $multiclan = false;

    public function mount(): void
    {
        $this->checkAuthorization();
        $this->initializeProperties();
    }

    #[Computed]
    public function maps(): Collection
    {
        return Map::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    #[Computed]
    public function centralPoints(): Collection
    {
        $mapId = $this->normalizeNullableInt($this->map_id);

        if (! $mapId) {
            return collect();
        }

        return CentralPoint::query()
            ->where('map_id', $mapId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    #[Computed]
    public function factions(): array
    {
        return FactionTypeEnum::cases();
    }

    public function save(): RedirectResponse|Redirector|null
    {
        $this->checkAuthorization();

        $this->map_id = $this->normalizeNullableInt($this->map_id);
        $this->central_point_id = $this->normalizeNullableInt($this->central_point_id);
        $this->faction = $this->normalizeFaction($this->faction);

        $this->validate();

        $type = 'success';
        $message = '';
        DB::beginTransaction();
        try {

            $this->roster->update([
                'name' => $this->name,
                'description' => $this->description,
                'map_id' => $this->map_id,
                'central_point_id' => $this->central_point_id,
                'faction' => $this->faction,
                'is_public' => $this->is_public,
                'multiclan' => $this->multiclan,
            ]);

            if ($this->image) {
                $oldImage = $this->roster->image;
                $newImagePath = $this->image->store('rosters', 'public');

                $this->roster->update([
                    'image' => $newImagePath,
                ]);

                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            DB::commit();
            $message = __('hll.clans.rosters.edit.message_success', ['name' => $this->roster->name]);
        } catch(\Throwable $th) {
            Log::error("Error al actualizar un roster. {$th->getMessage()} | {$th->getFile()} | {$th->getLine()}");
            DB::rollback();
            $type = 'error';
            $message = __('hll.clans.rosters.edit.error_transaction');
        }

        return redirect()->route('rosters.table', ['clan' => $this->clan->slug])->with($type, $message);
    }

    public function updatedMapId(int|string|null $value): void
    {
        $this->map_id = $this->normalizeNullableInt($value);
        $this->central_point_id = null;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'map_id' => ['nullable', 'integer', 'exists:maps,id'],
            'central_point_id' => ['nullable', Rule::exists('central_points', 'id')->where(function ($query) {
                $query->where('map_id', $this->map_id);
            })],
            'faction' => ['nullable', new Enum(FactionTypeEnum::class)],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function getValidationAttributes(): array
    {
        return __('hll.clans.rosters.form');
    }

    private function checkAuthorization(): void
    {
        abort_unless(
            $this->canUpdateRoster(),
            403,
            __('hll.clans.rosters.403')
        );
    }

    protected function initializeProperties(): void
    {
        $this->name = $this->roster->name;
        $this->description = $this->roster->description;
        $this->map_id = $this->normalizeNullableInt($this->roster->map_id);
        $this->central_point_id = $this->normalizeNullableInt($this->roster->central_point_id);
        $this->faction = $this->normalizeFaction($this->roster->faction);
        $this->is_public = $this->roster->is_public;
        $this->multiclan = $this->roster->multiclan;
    }

    private function canUpdateRoster(): bool
    {
        $user = auth()->user();

        return $user?->can('update', $this->roster) ?? false;
    }

    private function normalizeNullableInt(int|string|null $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeFaction(string|FactionTypeEnum|null $value): ?string
    {
        if ($value instanceof FactionTypeEnum) {
            return $value->value;
        }

        if ($value === '') {
            return null;
        }

        return $value;
    }
};
