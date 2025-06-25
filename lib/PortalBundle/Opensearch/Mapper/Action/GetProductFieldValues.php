<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Product;

final class GetProductFieldValues
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @return array<int, string>
     */
    public function __invoke(AssetResource $parentAssetResource, array $mapping, string $filterName): array
    {
        if (!in_array(needle: $filterName, haystack: array_keys($mapping))) {
            return [];
        }

        $values = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            $value = match ($filterName) {
                'product_ean', 'product_ean_text' => $product->getEAN() ?? '',
                'product_name', 'product_name_text' => $product->getName() ?? '',
                'product_sku', 'product_sku_text' => $product->getSKU() ?? '',

                default => ''
            };

            $values[] = $value;
        }

        return array_values(array_unique(array_filter($values, fn (?string $value) => $value !== null)));
    }
}
