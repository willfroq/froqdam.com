<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Pimcore\Model\DataObject\Data\CalculatedValue;
use Webmozart\Assert\Assert;

final class AssetResourceExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $key,
        public readonly string $path,
        public readonly string $assetFolderName,
        public readonly ?AssetDto $assetDto,
        /** @var array<int, AssetResourceExportItem> */
        public readonly array $children,
        public readonly int $validFrom,
        public readonly int $validUntil,
        public readonly int $fileCreateDate,
        public readonly int $fileModifyDate,
        public readonly int $assetTypeId,
        public readonly string $assetTypePath,
        public readonly string $assetTypeKey,
        public readonly string $uploadName,
        public readonly int $assetVersion,
        public readonly string|CalculatedValue|null $highestVersionNumber,
        /** @var array<int|string, mixed> */
        public readonly array $metadata,
        public readonly string $pdfText,
        public readonly string $embeddedMetadata,
        public readonly string $exifData,
        public readonly string $xmpData,
        public readonly string $iptcData,
        /** @var array<int, AssetResourceExportItem> */
        public readonly array $contains,
        /** @var array<int, AssetResourceExportItem> */
        public readonly array $usedIn,
        /** @var array<int, ProjectExportItem> */
        public readonly array $projects,
        /** @var array<int, ProductExportItem> */
        public readonly array $products,
        /** @var array<int, TagExportItem> */
        public readonly array $tags,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->assetFolderName, 'Expected "assetFolderName" to be a string, got %s');
        Assert::nullOrIsInstanceOf($this->assetDto, AssetDto::class, 'Expected "assetDto" to be an instance of AssetDto, got %s');
        Assert::isArray($this->children, 'Expected "children" to be an array, got %s');
        Assert::numeric($this->validFrom, 'Expected "validFrom" to be a numeric, got %s');
        Assert::numeric($this->validUntil, 'Expected "validUntil" to be a numeric, got %s');
        Assert::numeric($this->fileCreateDate, 'Expected "fileCreateDate" to be a numeric, got %s');
        Assert::numeric($this->fileModifyDate, 'Expected "fileModifyDate" to be a numeric, got %s');
        Assert::numeric($this->assetTypeId, 'Expected "fileModifyDate" to be a numeric, got %s');
        Assert::string($this->assetTypePath, 'Expected "assetTypePath" to be a string, got %s');
        Assert::string($this->assetTypeKey, 'Expected "assetTypeKey" to be a string, got %s');
        Assert::string($this->uploadName, 'Expected "uploadName" to be a string, got %s');
        Assert::numeric($this->assetVersion, 'Expected "assetVersion" to be a string, got %s');
        Assert::nullOrString($this->highestVersionNumber, 'Expected "highestVersionNumber" to be a string, got %s');
        Assert::isArray($this->metadata, 'Expected "metadata" to be a string, got %s');
        Assert::string($this->pdfText, 'Expected "pdfText" to be a string, got %s');
        Assert::string($this->embeddedMetadata, 'Expected "embeddedMetadata" to be a string, got %s');
        Assert::string($this->exifData, 'Expected "exifData" to be a string, got %s');
        Assert::string($this->xmpData, 'Expected "xmpData" to be a string, got %s');
        Assert::string($this->iptcData, 'Expected "iptcData" to be a string, got %s');
        Assert::isArray($this->contains, 'Expected "contains" to be a string, got %s');
        Assert::isArray($this->usedIn, 'Expected "usedIn" to be a string, got %s');
        Assert::isArray($this->projects, 'Expected "projects" to be a string, got %s');
        Assert::isArray($this->products, 'Expected "products" to be a string, got %s');
        Assert::isArray($this->tags, 'Expected "tags" to be a string, got %s');
    }
}
