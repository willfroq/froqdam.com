<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Enum;

enum ProductContents: int
{
    case NetContents = 1;
    case NetContentsMillilitre = 2;
    case NetContentsGrams = 3;
    case NetContentsPieces = 4;
    case NetContentsEach = 5;
    case NetUnitContents = 6;
    case NetUnitContentsMillilitre = 7;
    case NetUnitContentsGrams = 8;

    public function readable(): string
    {
        return match ($this) {
            self::NetContents => 'net_contents',
            self::NetContentsMillilitre => 'net_contents_ml',
            self::NetContentsGrams => 'net_contents_g',
            self::NetContentsPieces => 'net_contents_pc',
            self::NetContentsEach => 'net_contents_ea',
            self::NetUnitContents => 'net_unit_contents',
            self::NetUnitContentsMillilitre => 'net_unit_contents_ml',
            self::NetUnitContentsGrams => 'net_unit_contents_g',
        };
    }
}
