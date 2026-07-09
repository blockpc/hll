<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\RosterTypeSquadEnum;
use RuntimeException;

final class SquadCapacityExceededException extends RuntimeException
{
    public static function forType(RosterTypeSquadEnum $type): self
    {
        return new self(
            "Cannot create squad: the {$type->label()} capacity of {$type->capacity()} has been reached."
        );
    }
}
