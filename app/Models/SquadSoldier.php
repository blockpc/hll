<?php

namespace App\Models;

use App\Enums\RoleSquadTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquadSoldier extends Model
{
    /** @use HasFactory<\Database\Factories\SquadSoldierFactory> */
    use HasFactory;

    protected $fillable = [
        'squad_id',
        'soldier_id',
        'display_name',
        'slot_number',
        'role_squad_type',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role_squad_type' => RoleSquadTypeEnum::class,
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (self $squadSoldier): void {
            self::query()
                ->where('squad_id', $squadSoldier->squad_id)
                ->where('slot_number', '>', $squadSoldier->slot_number)
                ->decrement('slot_number');
        });
    }

    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    public function soldier(): BelongsTo
    {
        return $this->belongsTo(Soldier::class);
    }
}
