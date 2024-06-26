<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Enum;

enum AssetResourceOrganizationFolderNames: int
{
    case Assets = 1;
    case ThreeDModelLibrary = 2;
    case Cutter_Guides = 3;
    case Mockups = 4;
    case Products = 5;
    case Projects = 6;
    case Tags = 7;
    case Categories = 8;
    case Packshots = 9;
    case Printers = 10;
    case Suppliers = 11;

    public function readable(): string
    {
        return match ($this) {
            self::Assets => 'Assets',
            self::ThreeDModelLibrary => '3D_Model_Library',
            self::Cutter_Guides => 'Cutter_Guides',
            self::Mockups => 'Mockups',
            self::Products => 'Products',
            self::Projects => 'Projects',
            self::Tags => 'Tags',
            self::Categories => 'Categories',
            self::Packshots => 'Packshots',
            self::Printers => 'Printers',
            self::Suppliers => 'Suppliers',
        };
    }
}
