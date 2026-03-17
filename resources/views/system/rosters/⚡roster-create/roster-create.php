<?php

use App\Enums\FactionTypeEnum;
use App\Models\CentralPoint;
use App\Models\Clan;
use App\Models\Map;
use App\Models\Roster;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Crear Roster')] class extends Component
{
    use WithFileUploads;

    public Clan $clan;

    public string $name = '';

    public string $slug = '';

    public ?TemporaryUploadedFile $image = null;

    public ?string $description = null;

    public int|string|null $map_id = null;

    public int|string|null $central_point_id = null;

    public string|FactionTypeEnum|null $faction = null;

    public function mount(): void
    {
        $this->checkAuthorization();
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

    public function save(): void
    {
        $this->checkAuthorization();

        $this->slug = $this->normalizeRosterName($this->slug);

        $this->map_id = $this->normalizeNullableInt($this->map_id);
        $this->central_point_id = $this->normalizeNullableInt($this->central_point_id);
        $this->faction = $this->normalizeFaction($this->faction);

        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('rosters', 'public');
        }

        try {
            Roster::create([
                'clan_id' => $this->clan->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'map_id' => $this->map_id,
                'central_point_id' => $this->central_point_id,
                'faction' => $this->faction,
                'image' => $imagePath,
            ]);
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        session()->flash('success', __('hll.clans.rosters.create.message_success', ['name' => $this->name]));

        $this->redirectRoute('rosters.table', ['clan' => $this->clan->slug]);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:rosters,name,NULL,id,clan_id,' . $this->clan->id],
            'slug' => ['required', 'string', 'max:100', Rule::unique('rosters', 'slug')->where(function ($query) {
                return $query->where('clan_id', $this->clan->id);
            })],
            'description' => ['nullable', 'string', 'max:255'],
            'map_id' => ['required', 'integer', 'exists:maps,id'],
            'central_point_id' => ['required', Rule::exists('central_points', 'id')->where(function ($query) {
                $query->where('map_id', $this->normalizeNullableInt($this->map_id));
            })],
            'faction' => ['required', new Enum(FactionTypeEnum::class)],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function updatedMapId(int|string|null $value): void
    {
        $this->map_id = $this->normalizeNullableInt($value);
        $this->central_point_id = null;
    }

    protected function getValidationAttributes(): array
    {
        return __('hll.clans.rosters.form');
    }

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    private function checkAuthorization(): void
    {
        abort_unless(
            $this->canCreateRoster(),
            403,
            __('hll.clans.rosters.403')
        );
    }

    private function canCreateRoster(): bool
    {
        $user = auth()->user();

        return $user?->can('create', [Roster::class, $this->clan]) ?? false;
    }

    private function normalizeRosterName(string $name): string
    {
        return Str::slug(Str::transliterate(Str::lower(trim($name))));
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
