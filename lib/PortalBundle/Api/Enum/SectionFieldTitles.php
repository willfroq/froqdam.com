<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Enum;

enum SectionFieldTitles: int
{
    case AssetInfoSectionTitle = 1;
    case SkuSectionTitle = 2;
    case ProjectSectionTitle = 3;
    case SupplierSectionTitle = 4;
    case PrintSectionTitle = 5;

    public function readable(): string
    {
        return match ($this) {
            self::AssetInfoSectionTitle => 'assetInfoSectionTitle',
            self::SkuSectionTitle => 'skuSectionTitle',
            self::ProjectSectionTitle => 'projectSectionTitle',
            self::SupplierSectionTitle => 'supplierSectionTitle',
            self::PrintSectionTitle => 'printSectionTitle',
        };
    }
}
