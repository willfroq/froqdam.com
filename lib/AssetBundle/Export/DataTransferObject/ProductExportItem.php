<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class ProductExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $key,
        public readonly string $path,
        public readonly string $name,
        public readonly string $sku,
        public readonly string $ean,
        /** @var array<int|string, mixed> */
        public readonly array $attributes,
        public readonly string $netContentStatement,
        /** @var array<int|string, mixed> */
        public readonly array $netContents,
        /** @var array<int|string, mixed> */
        public readonly array $netUnitContents,
        /** @var array<int, CategoryExportItem> */
        public readonly array $categories,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->sku, 'Expected "sku" to be a string, got %s');
        Assert::string($this->ean, 'Expected "ean" to be a string, got %s');
        Assert::isArray($this->attributes, 'Expected "attributes" to be an array, got %s');
        Assert::string($this->netContentStatement, 'Expected "netContentStatement" to be a string, got %s');
        Assert::isArray($this->netContents, 'Expected "netContents" to be an array, got %s');
        Assert::isArray($this->netUnitContents, 'Expected "netUnitContents" to be an array, got %s');
        Assert::isArray($this->categories, 'Expected "categories" to be an array, got %s');
    }
}
