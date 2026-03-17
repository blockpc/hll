<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Pivots\ClanUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    #[Scope]
    protected function visibleToUser(Builder $query): void
    {
        $superAdminRole = (string) config('permission.super_admin_role', 'sudo');
        if (auth()->user()?->hasRole($superAdminRole)) {
            return;
        }

        $query->withoutRole([$superAdminRole]);
    }

    #[Scope]
    protected function search(Builder $query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->whereLike(['name', 'email'], $search);
    }

    #[Scope]
    protected function notCurrentUser(Builder $query): void
    {
        if (! auth()->check()) {
            return;
        }

        $query->where('id', '!=', auth()->id());
    }

    /**
     * Get the clan owned by the user.
     */
    public function ownedClan(): HasOne
    {
        return $this->hasOne(Clan::class, 'owner_user_id');
    }

    /**
     * The clans the user belongs to, including the role of the user in each clan (owner, helper).
     */
    public function clans(): BelongsToMany
    {
        return $this->belongsToMany(Clan::class)
            ->using(ClanUser::class)
            ->withPivot('membership_role')
            ->withTimestamps();
    }
}
