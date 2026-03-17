<?php

declare(strict_types=1);

namespace App\Enums;

enum SideTypeEnum : string
{
    case Allies = 'allies';
    case Axis = 'axis';

    public function label(): string
    {
        return match ($this) {
            self::Allies => __('hll.side_type.allies'),
            self::Axis => __('hll.side_type.axis'),
        };
    }

}
