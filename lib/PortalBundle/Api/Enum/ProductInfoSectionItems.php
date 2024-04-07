<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Enum;

enum ProductInfoSectionItems: int
{
    case ProductName = 1;
    case ProductSku = 2;
    case ProductEan = 3;
    case ProductCategorySegment = 4;
    case ProductCategoryBrand = 5;
    case ProductCategoryCampaign = 6;
    case ProductCategoryMarket = 7;
    case ProductCategoryPlatform = 8;

    public function readable(): string
    {
        return match ($this) {
            self::ProductName => 'product_name',
            self::ProductSku => 'product_sku',
            self::ProductEan => 'product_ean',
            self::ProductCategorySegment => 'product_category_segment',
            self::ProductCategoryBrand => 'product_category_brand',
            self::ProductCategoryCampaign => 'product_category_campaign',
            self::ProductCategoryMarket => 'product_category_market',
            self::ProductCategoryPlatform => 'product_category_platform',
        };
    }
}
