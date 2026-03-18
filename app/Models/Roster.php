<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FactionTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Roster extends Model
{
    /** @use HasFactory<\Database\Factories\RosterFactory> */
    use HasFactory;

    protected $fillable = [
        'clan_id',
        'name',
        'slug',
        'description',
        'faction',
        'map_id',
        'central_point_id',
        'image',
        'is_public',
        'multiclan',
    ];

    protected function casts(): array
    {
        return [
            'faction' => FactionTypeEnum::class,
            'is_public' => 'boolean',
            'multiclan' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $clan = request()->route('clan');

        if (! $clan instanceof Clan || ! isset($clan->id)) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('slug', $value)
            ->where('clan_id', $clan->id);
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
}
