<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Webmozart\Assert\Assert;

final class AssetItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $assetType,
        public readonly string $assetDocumentUrl,
        public readonly string $assetImageUrl,
        public readonly string $assetTextUrl,
        /** @var array<int, string|null>|string */
        public readonly string|array $assetExtension,
        public readonly string $portalAssetPath,
        public readonly string $assetFilename,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        Assert::string($this->assetDocumentUrl, 'Expected "assetDocumentUrl" to be a string, got %s');
        Assert::string($this->assetImageUrl, 'Expected "assetImageUrl" to be a string, got %s');
        Assert::string($this->assetTextUrl, 'Expected "assetTextUrl" to be a string, got %s');
        Assert::string($this->assetExtension, 'Expected "assetExtension" to be a string, got %s');
        Assert::string($this->portalAssetPath, 'Expected "portalAssetPath" to be a string, got %s');
        Assert::string($this->assetFilename, 'Expected "assetType" to be a string, got %s');
    }
}
