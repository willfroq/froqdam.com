<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class GroupAssetLibrarySettingsExportItem
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $key,
        public readonly ?string $path,
        public readonly ?string $groupName,

        /** @var array<int<0, max>, array<string, array<int, BlockElementDto>>> */
        public readonly array $assetLibraryFilterOptions,
        /** @var array<int<0, max>, array<string, array<int, BlockElementDto>>> */
        public readonly array $assetLibraryColumnsOptions,
        /** @var array<int<0, max>, array<string, array<int, BlockElementDto>>> */
        public readonly array $assetLibrarySortOptions,

        public readonly ?string $assetInfoSectionTitle,
        public readonly ?bool $isAssetInfoSectionEnabled,
        /** @var array<int|string, mixed> */
        public readonly array $assetInfoSectionItems,
        /** @var array<string, string> */
        public readonly array $assetInfoSectionMetadata,

        public readonly ?string $skuSectionTitle,
        public readonly ?bool $isSKUSectionEnabled,
        /** @var array<int|string, mixed> */
        public readonly array $skuSectionItems,
        /** @var array<string, string> */
        public readonly array $skuInfoSectionAttributes,

        public readonly ?string $projectSectionTitle,
        public readonly ?bool $isProjectSectionEnabled,
        /** @var array<int|string, mixed> */
        public readonly array $projectSectionItems,

        public readonly ?string $supplierSectionTitle,
        public readonly ?bool $isSupplierSectionEnabled,
        /** @var array<int|string, mixed> */
        public readonly array $supplierSectionItems,

        public readonly ?string $printSectionTitle,
        public readonly ?bool $isPrintSectionEnabled,
        /** @var array<int|string, mixed> */
        public readonly array $printSectionItems,
    ) {
        Assert::nullOrInteger($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::nullOrString($this->key, 'Expected "key" to be a string, got %s');
        Assert::nullOrString($this->path, 'Expected "path" to be a string, got %s');
        Assert::nullOrString($this->groupName, 'Expected "groupName" to be a string, got %s');

        Assert::isArray($this->assetLibraryFilterOptions, 'Expected "assetLibraryFilterOptions" to be an array, got %s');
        Assert::isArray($this->assetLibraryColumnsOptions, 'Expected "assetLibraryColumnsOptions" to be an array, got %s');
        Assert::isArray($this->assetLibrarySortOptions, 'Expected "assetLibrarySortOptions" to be an array, got %s');

        Assert::nullOrString($this->assetInfoSectionTitle, 'Expected "assetInfoSectionTitle" to be a string, got %s');
        Assert::nullOrBoolean($this->isAssetInfoSectionEnabled, 'Expected "isAssetInfoSectionEnabled" to be a bool, got %s');
        Assert::isArray($this->assetInfoSectionItems, 'Expected "assetInfoSectionItems" to be an array, got %s');
        Assert::isArray($this->assetInfoSectionMetadata, 'Expected "assetInfoSectionMetadata" to be an array, got %s');

        Assert::nullOrString($this->skuSectionTitle, 'Expected "skuSectionTitle" to be a string, got %s');
        Assert::nullOrBoolean($this->isSKUSectionEnabled, 'Expected "isSKUSectionEnabled" to be a bool, got %s');
        Assert::isArray($this->skuSectionItems, 'Expected "skuSectionItems" to be an array, got %s');
        Assert::isArray($this->skuInfoSectionAttributes, 'Expected "skuSectionItems" to be an array, got %s');

        Assert::nullOrString($this->projectSectionTitle, 'Expected "projectSectionTitle" to be a string, got %s');
        Assert::nullOrBoolean($this->isProjectSectionEnabled, 'Expected "isProjectSectionEnabled" to be a bool, got %s');
        Assert::isArray($this->projectSectionItems, 'Expected "projectSectionItems" to be an array, got %s');

        Assert::nullOrString($this->supplierSectionTitle, 'Expected "supplierSectionTitle" to be a string, got %s');
        Assert::nullOrBoolean($this->isSupplierSectionEnabled, 'Expected "isSupplierSectionEnabled" to be a bool, got %s');
        Assert::isArray($this->supplierSectionItems, 'Expected "supplierSectionItems" to be an array, got %s');

        Assert::nullOrString($this->printSectionTitle, 'Expected "printSectionTitle" to be a string, got %s');
        Assert::nullOrBoolean($this->isPrintSectionEnabled, 'Expected "isPrintSectionEnabled" to be a bool, got %s');
        Assert::isArray($this->printSectionItems, 'Expected "printSectionItems" to be an array, got %s');
    }
}
