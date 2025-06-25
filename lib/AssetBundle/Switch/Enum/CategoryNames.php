<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Enum;

enum CategoryNames: int
{
    case Brand = 1;
    case Campaign = 2;
    case Market = 3;
    case Segment = 4;
    case Platform = 5;

    public function readable(): string
    {
        return match ($this) {
            self::Brand => 'Brand',
            self::Campaign => 'Campaign',
            self::Market => 'Market',
            self::Segment => 'Segment',
            self::Platform => 'Platform',
        };
    }
}
