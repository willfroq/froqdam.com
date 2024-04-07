<?php

declare(strict_types=1);

namespace Froq\AssetBundle\ValueObject;

use Webmozart\Assert\Assert;

final class PayloadFromSwitch
{
    public function __construct(
        public readonly string $filename,
        public readonly string $customerCode,
        public readonly string $customAssetFolder,
        public readonly string $importTags,
        public readonly string $categoriseProduct,
        public readonly string $categoriseBrand,
        public readonly string $categoriseCampaign,
        public readonly string $categoriseMarket,
        public readonly string $categoriseSegment,
        public readonly string $categorisePlatform,
        public readonly string $importProductAttributes,
        public readonly string $importProductContent,
        public readonly string $assetType,
        public readonly bool $assetDoesNotExists,
        public readonly string $fileContents,
        public readonly string $importTagsMetadata,
        public readonly string $metadataFrom,
    ) {
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::string($this->customerCode, 'Expected "customerCode" to be a string, got %s');
        Assert::string($this->customAssetFolder, 'Expected "customAssetFolder" to be a string, got %s');
        Assert::string($this->importTags, 'Expected "importTags" to be a string, got %s');
        Assert::string($this->categoriseProduct, 'Expected "categoriseProduct" to be a string, got %s');
        Assert::string($this->categoriseBrand, 'Expected "categoriseBrand" to be a string, got %s');
        Assert::string($this->categoriseCampaign, 'Expected "categoriseCampaign" to be a string, got %s');
        Assert::string($this->categoriseMarket, 'Expected "categoriseCampaign" to be a string, got %s');
        Assert::string($this->categoriseSegment, 'Expected "categoriseSegment" to be a string, got %s');
        Assert::string($this->categorisePlatform, 'Expected "categorisePlatform" to be a string, got %s');
        Assert::string($this->importProductAttributes, 'Expected "importProductAttributes" to be a string, got %s');
        Assert::string($this->importProductContent, 'Expected "importProductContent" to be a string, got %s');
        Assert::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        Assert::boolean($this->assetDoesNotExists, 'Expected "assetDoesNotExists" to be a boolean, got %s');
        Assert::string($this->fileContents, 'Expected "fileContents" to be a string, got %s');
        Assert::string($this->importTagsMetadata, 'Expected "importTagsMetadata" to be a string, got %s');
        Assert::string($this->metadataFrom, 'Expected "metadataFrom" to be a string, got %s');
    }
}
