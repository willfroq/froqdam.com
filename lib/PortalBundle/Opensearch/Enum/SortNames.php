<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Enum;

enum SortNames: int
{
    case Asc = 1;
    case Desc = 2;

    public function readable(): string
    {
        return match ($this) {
            self::Asc => 'asc',
            self::Desc => 'desc',
        };
    }
}
