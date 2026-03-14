<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as ModelsRole;

final class Role extends ModelsRole
{
    use HasFactory;

    protected $fillable = ['name', 'display_name', 'description', 'guard_name', 'is_editable'];

    protected function casts(): array
    {
        return [
            'is_editable' => 'boolean',
        ];
    }

    #[Scope]
    public function visibleToUser(Builder $query): Builder
    {
        $superAdminRole = (string) config('permission.super_admin_role', 'sudo');
        if (auth()->user() && auth()->user()->hasRole($superAdminRole)) {
            return $query;
        }

        return $query->where('name', '!=', $superAdminRole);
    }

    #[Scope]
    public function search(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->whereLike(['name', 'display_name', 'description'], $search);
    }
}
