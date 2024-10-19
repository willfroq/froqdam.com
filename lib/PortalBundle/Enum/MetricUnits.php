<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Enum;

enum MetricUnits: int
{
    case Millilitre = 1;
    case Litre = 2;
    case Centilitre = 3;
    case Milligrams = 4;
    case Grams = 5;
    case Kilograms = 6;
    case Pieces = 7;
    case Each = 8;

    public function readable(): string
    {
        return match ($this) {
            self::Millilitre => 'ml',
            self::Litre => 'l',
            self::Centilitre => 'cl',
            self::Milligrams => 'mg',
            self::Grams => 'g',
            self::Kilograms => 'kg',
            self::Pieces => 'pcs',
            self::Each => 'ea',
        };
    }
}
