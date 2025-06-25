<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Product;

final class GetProductNetContentStatementValues
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @return array<int, string>
     */
    public function __invoke(AssetResource $parentAssetResource, array $mapping, string $filterKey): array
    {
        if (!in_array(needle: $filterKey, haystack: array_keys($mapping))) {
            return [];
        }

        $netContentStatementValues = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            $netContentStatementValues[] = $product->getNetContentStatement();
        }

        return array_values(array_unique(array_filter($netContentStatementValues, fn (?string $netContentStatementValue) => $netContentStatementValue !== null)));
    }
}
