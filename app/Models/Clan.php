<?php

namespace App\Models;

use App\Enums\ClanMembershipRoleEnum;
use App\Models\Pivots\ClanUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Clan extends Model
{
    /** @use HasFactory<\Database\Factories\ClanFactory> */
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'alias',
        'name',
        'slug',
        'description',
        'discord_url',
        'logo',
        'image',
    ];

    /**
     * Get the owner of the clan.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The users that belong to the clan, including the role of the user in the clan (owner, helper).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ClanUser::class)
            ->withPivot('membership_role')
            ->withTimestamps();
    }

    /**
     * The users that belong to the clan with the role of helper.
     */
    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ClanUser::class)
            ->withPivot('membership_role')
            ->withTimestamps()
            ->wherePivot('membership_role', ClanMembershipRoleEnum::Helper->value);
    }

    /**
     * Get the full URL to the clan's logo.
     */
    public function logoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->logo ? Storage::disk('public')->url($this->logo) : null
        );
    }

    /**
     * Get the full URL to the clan's image.
     */
    public function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image ? Storage::disk('public')->url($this->image) : null
        );
    }
}
