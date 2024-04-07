<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionAssetCreationDate;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionAssetTypeName;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionAssetVersion;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionCreationDate;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionLastModified;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionMetadataCollection;
use Webmozart\Assert\Assert;

final class SettingsItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $assetDetailConfigLabel,
        public readonly ?AssetInfoSectionAssetTypeName $assetInfoSectionAssetTypeName,
        public readonly ?AssetInfoSectionCreationDate $assetInfoSectionCreationDate,
        public readonly ?AssetInfoSectionAssetCreationDate $assetInfoSectionAssetCreationDate,
        public readonly ?AssetInfoSectionLastModified $assetInfoSectionLastModified,
        public readonly ?AssetInfoSectionAssetVersion $assetInfoSectionAssetVersion,
        public readonly ?AssetInfoSectionMetadataCollection $assetInfoSectionMetadata,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->assetDetailConfigLabel, 'Expected "assetDetailConfigLabel" to be a string, got %s');
        Assert::nullOrIsInstanceOf($this->assetInfoSectionAssetTypeName, AssetInfoSectionAssetTypeName::class, 'Expected "assetInfoSectionAssetTypeName" to be instanceof AssetInfoSectionAssetTypeName, got %s');
        Assert::nullOrIsInstanceOf($this->assetInfoSectionAssetCreationDate, AssetInfoSectionAssetCreationDate::class, 'Expected "assetInfoSectionAssetCreationDate" to be instanceof AssetInfoSectionAssetCreationDate, got %s');
        Assert::nullOrIsInstanceOf($this->assetInfoSectionLastModified, AssetInfoSectionLastModified::class, 'Expected "assetInfoSectionLastModified" to be instanceof AssetInfoSectionLastModified, got %s');
        Assert::nullOrIsInstanceOf($this->assetInfoSectionAssetVersion, AssetInfoSectionAssetVersion::class, 'Expected "assetInfoSectionAssetVersion" to be instanceof AssetInfoSectionAssetVersion, got %s');
        Assert::nullOrIsInstanceOf($this->assetInfoSectionMetadata, AssetInfoSectionMetadataCollection::class, 'Expected "assetInfoSectionMetadata" to be instanceof AssetInfoSectionMetadata, got %s');
    }
}
