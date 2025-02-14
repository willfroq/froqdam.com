<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\SetProductContents;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;

final class BuildProductContentsFromPayload
{
    public function __construct(private readonly SetProductContents $setProductContents)
    {
    }

    public function __invoke(Product $product, ProductFromPayload $productFromPayload, bool $isUpdate): void
    {
        $netContents = $productFromPayload->productNetContents;

        $keys = array_keys((array) $netContents);

        if (!in_array(needle: 'value', haystack: $keys) && !in_array(needle: 'attribute', haystack: $keys)) {
            $this->createNetContents((array) $netContents, $product);

            $this->createNetUnitContents((array) $netContents, $product);

            return;
        }

        if (isset($productFromPayload->productNetContents) && is_array($productFromPayload->productNetContents)) {
            ($this->setProductContents)($product, $productFromPayload->productNetContents, true, $isUpdate);
        }

        if (isset($productFromPayload->productNetUnitContents) && is_array($productFromPayload->productNetUnitContents)) {
            ($this->setProductContents)($product, $productFromPayload->productNetUnitContents, false, $isUpdate);
        }
    }

    /** @param array<int|string, mixed> $netContents */
    private function createNetContents(array $netContents, Product $product): void
    {
        if (empty($netContents)) {
            return;
        }

        $fieldCollectionItems = [];

        foreach ($netContents as $netContent) {
            $unitId = $netContent['attribute'] ?? null;
            $value = $netContent['value'] ?? null;

            if (is_null($unitId) || is_null($value)) {
                continue;
            }

            $productContents = new ProductContents();

            $quantityValue = new QuantityValue();
            $quantityValue->setUnitId($unitId);
            $quantityValue->setValue((float) $value);

            $productContents->setNetContent($quantityValue);

            $fieldCollectionItems[] = $productContents;
        }

        $productContentsFieldCollection = new Fieldcollection();
        $productContentsFieldCollection->setItems($fieldCollectionItems);

        $product->setNetContents($productContentsFieldCollection);
    }

    /** @param array<int|string, mixed> $netUnitContents */
    private function createNetUnitContents(array $netUnitContents, Product $product): void
    {
        if (empty($netUnitContents)) {
            return;
        }

        $fieldCollectionItems = [];

        foreach ($netUnitContents as $netUnitContent) {
            $unitId = $netUnitContent['attribute'] ?? null;
            $value = $netUnitContent['value'] ?? null;

            if (is_null($unitId) || is_null($value)) {
                continue;
            }

            $productContents = new ProductContents();

            $quantityValue = new QuantityValue();
            $quantityValue->setUnitId($unitId);
            $quantityValue->setValue((float) $value);

            $productContents->setNetContent($quantityValue);

            $fieldCollectionItems[] = $productContents;
        }

        $productContentsFieldCollection = new Fieldcollection();
        $productContentsFieldCollection->setItems($fieldCollectionItems);

        $product->setNetUnitContents($productContentsFieldCollection);
    }
}
