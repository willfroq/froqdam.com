<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\SetProductContents;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\QuantityValue\Unit;

final class BuildProductContentsFromPayload
{
    public function __construct(private readonly SetProductContents $setProductContents)
    {
    }

    public function __invoke(Product $product, ProductFromPayload $productFromPayload, bool $isUpdate): void
    {
        $netContents = (array) $productFromPayload->productNetContents;
        $netUnitContents = (array) $productFromPayload->productNetUnitContents;

        $netContentKeys = array_keys($netContents);
        $netUnitContentKeys = array_keys($netUnitContents);

        if (!in_array(needle: 'value', haystack: $netContentKeys) && !in_array(needle: 'attribute', haystack: $netContentKeys)) {
            $this->createNetContents($netContents, $product);
        }

        if (!in_array(needle: 'value', haystack: $netUnitContentKeys) && !in_array(needle: 'attribute', haystack: $netUnitContentKeys)) {
            $this->createNetUnitContents($netUnitContents, $product);
        }

        if (in_array(needle: 'value', haystack: $netContentKeys) && in_array(needle: 'attribute', haystack: $netContentKeys)) {
            ($this->setProductContents)($product, $netContents, true, $isUpdate);
        }

        if (in_array(needle: 'value', haystack: $netUnitContentKeys) && in_array(needle: 'attribute', haystack: $netUnitContentKeys)) {
            ($this->setProductContents)($product, $netUnitContents, false, $isUpdate);
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

            $unit = Unit::getByAbbreviation((string) $unitId);

            if (!($unit instanceof Unit)) {
                continue;
            }

            if (!is_numeric($value)) {
                continue;
            }

            $productContents = new ProductContents();

            $quantityValue = new QuantityValue();
            $quantityValue->setUnitId($unit->getId());
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

            $unit = Unit::getByAbbreviation((string) $unitId);

            if (!($unit instanceof Unit)) {
                continue;
            }

            if (!is_numeric($value)) {
                continue;
            }

            $productContents = new ProductContents();

            $quantityValue = new QuantityValue();
            $quantityValue->setUnitId($unit->getId());
            $quantityValue->setValue((float) $value);

            $productContents->setNetContent($quantityValue);

            $fieldCollectionItems[] = $productContents;
        }

        $productContentsFieldCollection = new Fieldcollection();
        $productContentsFieldCollection->setItems($fieldCollectionItems);

        $product->setNetUnitContents($productContentsFieldCollection);
    }
}
