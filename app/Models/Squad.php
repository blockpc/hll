<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RosterTypeSquadEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Squad extends Model
{
    /** @use HasFactory<\Database\Factories\SquadFactory> */
    use HasFactory;

    protected $fillable = [
        'roster_id',
        'name',
        'alias',
        'roster_type_squad',
        'pos_x',
        'pos_y',
        'z_index',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'roster_type_squad' => RosterTypeSquadEnum::class,
        ];
    }

    public function roster(): BelongsTo
    {
        return $this->belongsTo(Roster::class);
    }

    public function soldiers(): HasMany
    {
        return $this->hasMany(SquadSoldier::class)->orderBy('slot_number');
    }

    /**
     * Determine the maximum number of soldiers allowed in the squad based on its roster type.
     */
    protected function capacity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roster_type_squad->capacity()
        );
    }
}
