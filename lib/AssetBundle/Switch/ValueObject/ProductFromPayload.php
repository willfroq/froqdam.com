<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class ProductFromPayload
{
    public function __construct(
        public readonly ?string $productName,
        public readonly ?string $productEAN,
        public readonly ?string $productSKU,
        /** @var array<int, array<string, string>>|null $productAttributes */
        public readonly ?array $productAttributes,
        public readonly ?string $productNetContentStatement,
        /** @var array<string, float|int|string|null>|null $productNetContents */
        public readonly ?array $productNetContents,
        /** @var array<string, float|int|string|null>|null $productNetUnitContents */
        public readonly ?array $productNetUnitContents,
        public readonly ?CategoryFromPayload $productCategories,
    ) {
        Assert::nullOrString($this->productName, 'Expected "productName" to be a string, got %s');
        Assert::nullOrString($this->productEAN, 'Expected "productEAN" to be a string, got %s');
        Assert::nullOrString($this->productSKU, 'Expected "productSKU" to be a string, got %s');
        Assert::nullOrIsArray($this->productAttributes, 'Expected "productAttributes" to be a string, got %s');
        Assert::nullOrString($this->productNetContentStatement, 'Expected "productNetContentStatement" to be a string, got %s');
        Assert::nullOrIsArray($this->productNetContents, 'Expected "$productNetContents" to be a string, got %s');
        Assert::nullOrIsArray($this->productNetUnitContents, 'Expected "$productNetUnitContents" to be a string, got %s');
        Assert::nullOrIsInstanceOf($this->productCategories, CategoryFromPayload::class, 'Expected "$productNetUnitContents" to be a string, got %s');
    }
}
