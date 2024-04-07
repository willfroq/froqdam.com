<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action\ESPropertyMapping;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;

final class GetProductContentPropertyValues
{
    /** @return array<int, mixed> */
    public function __invoke(AssetResource $assetResource, bool $hasConfig, bool $isNetContent, string $metricUnit): array
    {
        if ($isNetContent) {
            $productNetContents = fn (Product $product) => $product->getNetContents();
        }

        if (!$isNetContent) {
            $productNetContents = fn (Product $product) => $product->getNetUnitContents();
        }

        if ($hasConfig) {
            /** @var AssetResource $assetResource */
            $assetResource = AssetResourceHierarchyHelper::getLatestVersion($assetResource);
        }

        /** @var Product[] $products */
        $products = $assetResource->getProducts();

        $values = [];

        foreach ($products as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            /** @var ProductContents $productContents */
            foreach ($productNetContents($product) ?? [] as $productContents) {
                $quantityValue = $productContents->getNetContent();

                if (!($quantityValue instanceof QuantityValue)) {
                    continue;
                }

                $values[] = (new GetMetricValue)($quantityValue, $metricUnit);
            }
        }

        return array_unique($values);
    }
}
