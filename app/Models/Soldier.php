<?php

namespace App\Models;

use App\Enums\RoleSquadTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Soldier extends Model
{
    /** @use HasFactory<\Database\Factories\SoldierFactory> */
    use HasFactory;

    protected $fillable = [
        'clan_id',
        'name',
        'role',
        'observation',
    ];

    // cuando un soldado es eliminado, se mantiene el registro del soldado en la tabla squad_soldiers con null en soldier_id y se guarda el display_name y slot_number para mantener la integridad de los datos históricos
    public static function boot(): void
    {
        parent::boot();

        static::deleting(function (Soldier $soldier) {
            $soldier->squads()->updateExistingPivot($soldier->squads->pluck('id')->toArray(), [
                'soldier_id' => null,
                'display_name' => $soldier->name,
            ]);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => RoleSquadTypeEnum::class,
        ];
    }

    public function clan(): BelongsTo
    {
        return $this->belongsTo(Clan::class);
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
     * a soldier can belong to multiple squads, but only one squad per roster
     *
     * @return BelongsToMany<Squad, $this>
     */
    public function squads(): BelongsToMany
    {
        return $this->belongsToMany(Squad::class, 'squad_soldiers')
            ->withPivot('slot_number', 'display_name')
            ->withTimestamps();
    }
}
