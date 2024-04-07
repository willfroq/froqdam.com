<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Enum;

enum StructuredTableNames: int
{
    case AssetInfoSectionItems = 1;
    case SkuSectionItems = 2;
    case ProjectSectionItems = 3;
    case SupplierSectionItems = 4;
    case PrintSectionItems = 5;

    public function readable(): string
    {
        return match ($this) {
            self::AssetInfoSectionItems => 'assetInfoSectionItems',
            self::SkuSectionItems => 'skuSectionItems',
            self::ProjectSectionItems => 'projectSectionItems',
            self::SupplierSectionItems => 'supplierSectionItems',
            self::PrintSectionItems => 'printSectionItems',
        };
    }
}
