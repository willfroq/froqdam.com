<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Enum;

enum AssetInfoSectionItems: int
{
    case AssetTypeName = 1;
    case CreationDate = 2;
    case AssetCreationDate = 3;
    case LastModified = 4;
    case AssetVersion = 5;

    public function readable(): string
    {
        return match ($this) {
            self::AssetTypeName => 'asset_type_name',
            self::CreationDate => 'creation_date',
            self::AssetCreationDate => 'asset_creation_date',
            self::LastModified => 'file_modify_date',
            self::AssetVersion => 'asset_version',
        };
    }
}
