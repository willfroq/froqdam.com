<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Webmozart\Assert\Assert;

final class ProductItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $productNameTableRowLabel,
        public readonly string $name,
        public readonly string $nameLink,
        public readonly string $skuTableRowLabel,
        public readonly string $sku,
        public readonly string $skuLink,
        public readonly string $eanTableRowLabel,
        public readonly string $ean,
        public readonly string $eanLink,
        public readonly CategoryHierarchies $categoryHierarchies,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->productNameTableRowLabel, 'Expected "productNameTableRowLabel" to be a string, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->nameLink, 'Expected "nameLink" to be a string, got %s');
        Assert::string($this->skuTableRowLabel, 'Expected "skuTableRowLabel" to be a string, got %s');
        Assert::string($this->sku, 'Expected "sku" to be a string, got %s');
        Assert::string($this->skuLink, 'Expected "skuLink" to be a string, got %s');
        Assert::string($this->eanTableRowLabel, 'Expected "eanTableRowLabel" to be a string, got %s');
        Assert::string($this->ean, 'Expected "ean" to be a string, got %s');
        Assert::string($this->eanLink, 'Expected "eanLink" to be a string, got %s');
        Assert::isInstanceOf($this->categoryHierarchies, CategoryHierarchies::class, 'Expected "categoryHierarchies" to be instance of CategoryHierarchies, got %s');
    }
}
