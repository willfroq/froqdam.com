<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\QuantityValue\Unit;

final class BuildProductContentsFromPayload
{
    /** @param array<string, array<string, float|int|string|null>> $payload */
    public function __invoke(Product $product, array $payload): void
    {
        if (isset($payload['productNetContents']) && is_array($payload['productNetContents'])) {
            $this->setProductContents($product, $payload['productNetContents'], true);
        }

        if (isset($payload['productNetUnitContents']) && is_array($payload['productNetUnitContents'])) {
            $this->setProductContents($product, $payload['productNetContents'], false);
        }
    }

    /** @param array<string, float|int|string|null> $payload */
    private function setProductContents(Product $product, array $payload, bool $isNetContent): void
    {
        $keys = array_keys($payload);

        if (!in_array(needle: 'value', haystack: $keys) || !in_array(needle: 'attribute', haystack: $keys)) {
            return;
        }

        $attribute = $payload['attribute'];
        $value = $payload['value'];

        $unit = Unit::getByAbbreviation((string) $attribute);

        if (!($unit instanceof Unit)) {
            return;
        }

        if (!is_numeric($value)) {
            return;
        }

        $productContents = new ProductContents();

        $quantityValue = new QuantityValue();
        $quantityValue->setUnitId($unit->getId());
        $quantityValue->setValue($value);

        $productContents->setNetContent($quantityValue);

        $fieldCollectionItems[] = $productContents;

        $productContentsFieldCollection = new Fieldcollection();
        $productContentsFieldCollection->setItems($fieldCollectionItems);

        if ($isNetContent) {
            $product->setNetContents($productContentsFieldCollection);
        }

        if (!$isNetContent) {
            $product->setNetUnitContents($productContentsFieldCollection);
        }
    }
}
