<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\QuantityValue\Unit;

final class SetProductContents
{
    /** @param array<string|int, mixed> $payload */
    public function __invoke(Product $product, array $payload, bool $isNetContent, bool $isUpdate): void
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
        $quantityValue->setValue((float) $value);

        $productContents->setNetContent($quantityValue);

        $fieldCollectionItems[] = $productContents;

        $productContentsFieldCollection = new Fieldcollection();
        $productContentsFieldCollection->setItems($fieldCollectionItems);

        if ($isUpdate) {
            if ($isNetContent && empty($product->getNetContents())) {
                $product->setNetContents($productContentsFieldCollection);
            }

            if (!$isNetContent && empty($product->getNetUnitContents())) {
                $product->setNetUnitContents($productContentsFieldCollection);
            }
        }

        if (!$isUpdate) {
            if ($isNetContent) {
                $product->setNetContents($productContentsFieldCollection);
            }

            if (!$isNetContent) {
                $product->setNetUnitContents($productContentsFieldCollection);
            }
        }
    }
}
