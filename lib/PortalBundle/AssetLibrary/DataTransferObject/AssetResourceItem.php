<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\DataTransferObject;

use Webmozart\Assert\Assert;

final class AssetResourceItem
{
    public function __construct(
        public int $assetResourceId,
        public int $assetId,
        public string $filename,
        public string $assetTypeName,
        public string $projectName,
        public string $assetResourceCreationDate,
        public string $assetResourceFileCreateDate,
        public string $assetResourceFileModifyDate,
        public string $assetCreationDate,
    ) {
        Assert::numeric($this->assetResourceId, 'Expected "assetResourceId" to be a numeric, got %s');
        Assert::numeric($this->assetId, 'Expected "assetResourceId" to be a numeric, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::string($this->assetTypeName, 'Expected "assetTypeName" to be a string, got %s');
        Assert::string($this->projectName, 'Expected "projectName" to be a string, got %s');
        Assert::string($this->assetResourceCreationDate, 'Expected "assetResourceCreationDate" to be a string, got %s');
        Assert::string($this->assetResourceFileCreateDate, 'Expected "assetResourceFileCreateDate" to be a string, got %s');
        Assert::string($this->assetResourceFileModifyDate, 'Expected "assetResourceFileModifyDate" to be a string, got %s');
        Assert::string($this->assetCreationDate, 'Expected "assetCreationDate" to be a string, got %s');
    }
}
