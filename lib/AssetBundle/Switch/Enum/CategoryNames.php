<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Enum;

enum CategoryNames: int
{
    case Brands = 1;
    case Campaigns = 2;
    case Markets = 3;
    case Segments = 4;
    case Platforms = 5;

    public function readable(): string
    {
        return match ($this) {
            self::Brands => 'Brands',
            self::Campaigns => 'Campaigns',
            self::Markets => 'Markets',
            self::Segments => 'Segments',
            self::Platforms => 'Platforms',
        };
    }
}
