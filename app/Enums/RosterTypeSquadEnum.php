<?php

declare(strict_types=1);

namespace App\Enums;

enum RosterTypeSquadEnum: string
{
    private const MAX_CUSTOM_SOLDIERS = 99;

    case Commander = 'commander';
    case Infantry = 'infantry';
    case Recon = 'recon';
    case Armor = 'armor';
    case Artillery = 'artillery';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Commander => __('hll.roster_type_squad.commander'),
            self::Infantry => __('hll.roster_type_squad.infantry'),
            self::Recon => __('hll.roster_type_squad.recon'),
            self::Armor => __('hll.roster_type_squad.armor'),
            self::Artillery => __('hll.roster_type_squad.artillery'),
            self::Custom => __('hll.roster_type_squad.custom'),
        };
    }

    /**
     * Determine the maximum number of soldiers allowed in the squad based on its roster type.
     */
    public function capacity(): int
    {
        return match ($this) {
            self::Commander => 1,
            self::Infantry => 6,
            self::Recon => 2,
            self::Armor => 3,
            self::Artillery => 3,
            self::Custom => self::MAX_CUSTOM_SOLDIERS,
        };
    }

    /**
     * Get the color associated with the squad type for UI purposes.
     */
    public function color(): string
    {
        return match ($this) {
            self::Commander => 'indigo',
            self::Infantry => 'green',
            self::Recon => 'pink',
            self::Armor => 'yellow',
            self::Artillery => 'blue',
            self::Custom => 'gray',
        };
    }

    /**
     * Get the prefix for the squad type to be used in display names or identifiers.
     */
    public function prefix(): string
    {
        return match ($this) {
            self::Commander => 'CMD',
            self::Infantry => 'INF',
            self::Recon => 'REC',
            self::Armor => 'ARM',
            self::Artillery => 'ART',
            self::Custom => 'CST',
        };
    }
}
