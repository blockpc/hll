<?php

declare(strict_types=1);

namespace App\Enums;

enum FactionTypeEnum: string
{
    case Allies = 'allies';
    case Axis = 'axis';

    public function label(): string
    {
        return match ($this) {
            self::Allies => __('hll.faction_type.allies'),
            self::Axis => __('hll.faction_type.axis'),
        };
    }
}
