<?php

declare(strict_types=1);

namespace App\Enums;

enum RosterTypeSquadEnum : string
{
    case Command = 'command';
    case Infantry = 'infantry';
    case Recon = 'recon';
    case Armor = 'armor';
    case Artyllery = 'artillery';

    public function label(): string
    {
        return match ($this) {
            self::Command => __('hll.roster_type_squad.command'),
            self::Infantry => __('hll.roster_type_squad.infantry'),
            self::Recon => __('hll.roster_type_squad.recon'),
            self::Armor => __('hll.roster_type_squad.armor'),
            self::Artyllery => __('hll.roster_type_squad.artillery'),
        };
    }

    public function maxSoldiers()
    {
        return match ($this) {
            self::Command => 1,
            self::Infantry => 6,
            self::Recon => 2,
            self::Armor => 3,
            self::Artyllery => 3,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Command => 'bg-blue-500',
            self::Infantry => 'bg-green-500',
            self::Recon => 'bg-yellow-500',
            self::Armor => 'bg-gray-500',
            self::Artyllery => 'bg-red-500',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::Command => 'CMD',
            self::Infantry => 'INF',
            self::Recon => 'REC',
            self::Armor => 'ARM',
            self::Artyllery => 'ART',
        };
    }
}
