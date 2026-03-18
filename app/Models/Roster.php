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
}
