<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Product;

final class GetProductAttributeValues
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @return array<int, string>
     */
    public function __invoke(AssetResource $parentAssetResource, array $mapping, string $attributeKey): array
    {
        if (!in_array(needle: $attributeKey, haystack: array_keys($mapping))) {
            return [];
        }

        $values = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            foreach ($product->getAttributes()?->getItems() ?? [] as $attributes) {
                if (!($attributes instanceof ProductAttributes)) {
                    continue;
                }

                if ($attributes->getAttributeKey() === $attributeKey) {
                    $values[] = $attributes->getAttributeValue();
                }
            }
        }

        return array_values(array_unique(array_filter($values, fn (?string $value) => $value !== null)));
    }
}
