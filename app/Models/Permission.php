<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as ModelsPermission;

final class Permission extends ModelsPermission
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'key',
        'guard_name',
    ];

    /**
     * Filter permissions by search term across name, display_name, description, and key.
     */
    #[Scope]
    protected function search(Builder $query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->whereAnyLike(['name', 'display_name', 'description', 'key'], $search);
    }

    /**
     * Scope a query to only include permissions visible to the current user.
     *
     * If the user does not have the 'sudo' role, exclude the 'super admin' permission.
     */
    #[Scope]
    protected function visibleToUser(Builder $query): void
    {
        $superAdminRole = (string) config('permission.super_admin_role', 'sudo');

        if (auth()->user()?->hasRole($superAdminRole)) {
            return;
        }

        $query->where('name', '!=', 'super admin');
    }

    /**
     * Scope a query to filter permissions by their key.
     */
    #[Scope]
    protected function byKey(Builder $query, ?string $key): void
    {
        if (empty($key)) {
            return;
        }

        $query->where('key', $key);
    }
}
