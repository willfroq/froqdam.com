<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Enum;

enum SortNames: int
{
    case Asc = 1;
    case Desc = 2;
    case CreationDate = 3;

    public function readable(): string
    {
        return match ($this) {
            self::Asc => 'asc',
            self::Desc => 'desc',
            self::CreationDate => 'creation_date',
        };
    }
}
