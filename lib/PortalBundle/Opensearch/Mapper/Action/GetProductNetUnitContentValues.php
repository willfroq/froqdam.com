<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;

final class GetProductNetUnitContentValues
{
    public function __construct(private readonly GetMetricValue $getMetricValue)
    {
    }

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

        $filterNames = explode('_', $filterKey);

        $metricUnit = end($filterNames);

        $values = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            /** @var ProductContents $productContents */
            foreach ($product->getNetUnitContents() ?? [] as $productContents) {
                $quantityValue = $productContents->getNetContent();

                if (!($quantityValue instanceof QuantityValue)) {
                    continue;
                }

                $value = ($this->getMetricValue)($quantityValue, $metricUnit);

                if (!$value) {
                    continue;
                }

                $values[] = $value.$quantityValue->getUnit()?->getAbbreviation();
            }
        }

        return array_values(array_unique(array_filter($values, fn (?string $value) => $value !== null)));
    }
}
