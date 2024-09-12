<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Enum;

enum MetricUnits: int
{
    case Millilitre = 1;
    case Litre = 2;
    case Grams = 3;
    case Kilograms = 4;
    case Pieces = 5;
    case Each = 6;

    public function readable(): string
    {
        return match ($this) {
            self::Millilitre => 'mlt',
            self::Litre => 'ltr',
            self::Grams => 'grm',
            self::Kilograms => 'kgm',
            self::Pieces => 'pcs',
            self::Each => 'ea',
        };
    }
}
