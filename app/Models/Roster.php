<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FactionTypeEnum;
use App\Enums\RosterTypeSquadEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Roster extends Model
{
    /** @use HasFactory<\Database\Factories\RosterFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'clan_id',
        'name',
        'description',
        'faction',
        'max_soldiers',
        'map_id',
        'central_point_id',
        'image',
        'is_public',
        'is_multiclan',
        'is_multifaction',
    ];

    protected function casts(): array
    {
        return [
            'faction' => FactionTypeEnum::class,
            'max_soldiers' => 'integer',
            'is_public' => 'boolean',
            'is_multiclan' => 'boolean',
            'is_multifaction' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $roster): void {
            if (! $roster->uuid) {
                $roster->uuid = (string) Str::orderedUuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function clan(): BelongsTo
    {
        return $this->belongsTo(Clan::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function centralPoint(): BelongsTo
    {
        return $this->belongsTo(CentralPoint::class);
    }

    #[Scope]
    protected function search(Builder $query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->whereAnyLike(['name'], $search);
    }

    /**
     * Get the full URL to the roster's image
     */
    public function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image ? Storage::disk('public')->url($this->image) : null
        );
    }

    public function squadSoldiers(): HasManyThrough
    {
        return $this->hasManyThrough(SquadSoldier::class, Squad::class);
    }

    /**
     * Count soldiers assigned to the roster across all squads.
     */
    public function assignedSoldiersCount(): int
    {
        return $this->squadSoldiers()->count();
    }

    public function squads(): HasMany
    {
        return $this->hasMany(Squad::class);
    }

    public function commandSquads(): HasMany
    {
        return $this->squads()->where('roster_type_squad', RosterTypeSquadEnum::Command);
    }

    public function infantrySquads(): HasMany
    {
        return $this->squads()->where('roster_type_squad', RosterTypeSquadEnum::Infantry);
    }

    public function customSquads(): HasMany
    {
        return $this->squads()->where('roster_type_squad', RosterTypeSquadEnum::Custom);
    }
}
