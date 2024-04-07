<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResource;

use Webmozart\Assert\Assert;

final class AssetResourceItem
{
    public function __construct(
        public readonly int $assetResourceId,
        public readonly string $thumbnailLink,
        public readonly string $filename,
        public readonly string $assetType,
        public readonly string $projectName,
        public readonly string $downloadLink,
        public readonly string $assetResourceCreationDate,
        public readonly string $assetCreationDate,
    ) {
        Assert::numeric($this->assetResourceId, 'Expected "assetResourceId" to be a numeric, got %s');
        Assert::string($this->thumbnailLink, 'Expected "thumbnailLink" to be a string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        Assert::string($this->projectName, 'Expected "projectName" to be a string, got %s');
        Assert::string($this->downloadLink, 'Expected "downloadLink" to be a string, got %s');
        Assert::string($this->assetResourceCreationDate, 'Expected "assetResourceCreationDate" to be a string, got %s');
        Assert::string($this->assetCreationDate, 'Expected "assetCreationDate" to be a string, got %s');
    }
}
