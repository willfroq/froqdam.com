<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\QuantityValue\Unit;

final class BuildProductContentsFromPayload
{
    public function __invoke(Product $product, ProductFromPayload $productFromPayload): void
    {
        if (isset($productFromPayload->productNetContents) && is_array($productFromPayload->productNetContents)) {
            $this->setProductContents($product, $productFromPayload->productNetContents, true);
        }

        if (isset($productFromPayload->productNetUnitContents) && is_array($productFromPayload->productNetUnitContents)) {
            $this->setProductContents($product, $productFromPayload->productNetUnitContents, false);
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
        $quantityValue->setValue((float) $value);

        $productContents->setNetContent($quantityValue);

        $fieldCollectionItems[] = $productContents;

        $productContentsFieldCollection = new Fieldcollection();
        $productContentsFieldCollection->setItems($fieldCollectionItems);

        if ($isNetContent && $product->getNetContents() === null) {
            $product->setNetContents($productContentsFieldCollection);
        }

        if (!$isNetContent && $product->getNetUnitContents() === null) {
            $product->setNetUnitContents($productContentsFieldCollection);
        }
    }
}
