<?php

namespace App\Models;

use App\Enums\RoleSquadTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
