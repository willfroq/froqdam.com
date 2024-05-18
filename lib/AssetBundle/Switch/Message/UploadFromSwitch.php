<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Message;

use Webmozart\Assert\Assert;

final class UploadFromSwitch
{
    public function __construct(
        public readonly string $eventName,
        public readonly string $filename,
        public readonly string $customerCode,
        public readonly ?string $customAssetFolder,
        public readonly string $assetType,
        public readonly string $assetResourceMetadataFieldCollection,
        public readonly string $productData,
        public readonly string $tagData,
        public readonly string $projectData,
        public readonly string $printerData,
        public readonly string $supplierData,
        public readonly string $temporaryFilePath,
    ) {
        Assert::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::string($this->customerCode, 'Expected "customerCode" to be a string, got %s');
        Assert::nullOrString($this->customAssetFolder, 'Expected "customAssetFolder" to be a string, got %s');
        Assert::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        Assert::string($this->assetResourceMetadataFieldCollection, 'Expected "assetResourceMetadataFieldCollection" to be a string, got %s');
        Assert::string($this->productData, 'Expected "productData" to be a string, got %s');
        Assert::string($this->tagData, 'Expected "tagData" to be a string, got %s');
        Assert::string($this->projectData, 'Expected "projectData" to be a string, got %s');
        Assert::string($this->printerData, 'Expected "printerData" to be a string, got %s');
        Assert::string($this->supplierData, 'Expected "supplierData" to be a string, got %s');
        Assert::string($this->temporaryFilePath, 'Expected "temporaryFilePath" to be a string, got %s');
    }
}
