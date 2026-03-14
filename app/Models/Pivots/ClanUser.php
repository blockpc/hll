<?php

declare(strict_types=1);

namespace App\Models\Pivots;

use App\Enums\ClanMembershipRoleEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class ClanUser extends Pivot
{
    /**
     * @return array<string, class-string>
     */
    protected function casts(): array
    {
        return [
            'membership_role' => ClanMembershipRoleEnum::class,
        ];
    }
}
