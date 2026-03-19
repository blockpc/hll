<?php

declare(strict_types=1);

namespace App\Enums;

enum RosterTypeSquadEnum: string
{
    private const UNLIMITED_SOLDIERS = 999;

    case Command = 'command';
    case Infantry = 'infantry';
    case Recon = 'recon';
    case Armor = 'armor';
    case Artillery = 'artillery';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Command => __('hll.roster_type_squad.command'),
            self::Infantry => __('hll.roster_type_squad.infantry'),
            self::Recon => __('hll.roster_type_squad.recon'),
            self::Armor => __('hll.roster_type_squad.armor'),
            self::Artillery => __('hll.roster_type_squad.artillery'),
            self::Custom => __('hll.roster_type_squad.custom'),
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
            self::Custom => self::UNLIMITED_SOLDIERS,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Command => 'indigo',
            self::Infantry => 'green',
            self::Recon => 'pink',
            self::Armor => 'yellow',
            self::Artillery => 'blue',
            self::Custom => 'gray',
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
            self::Custom => 'CST',
        };
    }
}
