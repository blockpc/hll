<?php

declare(strict_types=1);

namespace App\Enums;

enum RosterTypeSquadEnum: string
{
    case Command = 'command';
    case Infantry = 'infantry';
    case Recon = 'recon';
    case Armor = 'armor';
    case Artillery = 'artillery';

    public function label(): string
    {
        return match ($this) {
            self::Command => __('hll.roster_type_squad.command'),
            self::Infantry => __('hll.roster_type_squad.infantry'),
            self::Recon => __('hll.roster_type_squad.recon'),
            self::Armor => __('hll.roster_type_squad.armor'),
            self::Artillery => __('hll.roster_type_squad.artillery'),
        };
    }

    public function maxSoldiers(): int
    {
        return match ($this) {
            self::Command => 1,
            self::Infantry => 6,
            self::Recon => 2,
            self::Armor => 3,
            self::Artillery => 3,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Command => 'info',
            self::Infantry => 'success',
            self::Recon => 'ban',
            self::Armor => 'warning',
            self::Artillery => 'secondary',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::Command => 'CMD',
            self::Infantry => 'INF',
            self::Recon => 'REC',
            self::Armor => 'ARM',
            self::Artillery => 'ART',
        };
    }
}
