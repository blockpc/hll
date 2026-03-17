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
    protected function visibleToUser(Builder $query): void
    {
        $superAdminRole = (string) config('permission.super_admin_role', 'sudo');
        $user = auth()->user();
        if ($user && $user->hasRole($superAdminRole)) {
            return;
        }

        $query->where('name', '!=', $superAdminRole);
    }

    #[Scope]
    protected function search(Builder $query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->whereAnyLike(['name', 'display_name', 'description'], $search);
    }
}
