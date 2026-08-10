<?php

namespace App\Models;

use App\Enums\RoleSquadTypeEnum;
use Database\Factories\SoldierFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Soldier extends Model
{
    /** @use HasFactory<SoldierFactory> */
    use HasFactory;

    protected $fillable = [
        'clan_id',
        'name',
        'role',
        'rcon',
        'level',
        'observation',
    ];

    /**
     * When a soldier is deleted, preserve historical squad slot data by nulling `soldier_id`
     * on the pivot and storing the soldier's last known name in `display_name`.
     */
    protected static function booted(): void
    {
        static::deleting(static function (Soldier $soldier): void {

            $squadIds = $soldier->squads()->pluck('squads.id')->all();

            if ($squadIds === []) {
                return;
            }

            $soldier->squads()->updateExistingPivot($squadIds, [
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
            'level' => 'integer',
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

    /**
     * clan alias must be uppercase and soldier name must be ucfirst to show in public rosters
     */
    protected function aliasName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->clan->alias.'_'.ucfirst($this->name),
        );
    }
}
