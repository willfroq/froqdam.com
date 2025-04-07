<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class ProductInfoSectionItem
{
    public function __construct(
        public string $name,
        public string $sku,
        public string $ean,
        public string $contents,

        /** @var array<int, ProductNetContent> $netContents */
        public array $netContents,
        /** @var array<int, ProductNetUnitContent> $netUnitContents */
        public array $netUnitContents,
        /** @var array<int, CategoryItem> $brands */
        public array $brands,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->sku, 'Expected "sku" to be a string, got %s');
        Assert::string($this->ean, 'Expected "ean" to be a string, got %s');
        Assert::string($this->contents, 'Expected "contents" to be a string, got %s');
        Assert::isArray($this->netContents, 'Expected "netContents" to be a array, got %s');
        Assert::isArray($this->netUnitContents, 'Expected "netUnitContents" to be a array, got %s');
        Assert::isArray($this->brands, 'Expected "brands" to be a array, got %s');
    }
}
