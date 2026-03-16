<?php

declare(strict_types=1);

namespace App\Enums;

enum ClanMembershipRoleEnum: string
{
    case Owner = 'owner';
    case Helper = 'helper';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Administrador',
            self::Helper => 'Asistente',
        };
    }
}
