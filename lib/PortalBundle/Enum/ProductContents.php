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
            self::NetContentsMillilitre => 'net_contents_mlt',
            self::NetContentsGrams => 'net_contents_grm',
            self::NetContentsPieces => 'net_contents_pcs',
            self::NetContentsEach => 'net_contents_ea',
            self::NetUnitContents => 'net_unit_contents',
            self::NetUnitContentsMillilitre => 'net_unit_contents_mlt',
            self::NetUnitContentsGrams => 'net_unit_contents_grm',
        };
    }
}
