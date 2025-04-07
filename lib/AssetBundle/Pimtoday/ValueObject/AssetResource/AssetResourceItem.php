<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResource;

use Webmozart\Assert\Assert;

final class AssetResourceItem
{
    public function __construct(
        public int $assetResourceId,
        /** @var array<string, string> $thumbnailLinks */
        public array $thumbnailLinks,
        public string $filename,
        public string $assetType,
        public string $projectName,
        public string $downloadLink,
        public string $creationDate,
        public string $fileCreationDate,
    ) {
        Assert::numeric($this->assetResourceId, 'Expected "assetResourceId" to be a numeric, got %s');
        Assert::isArray($this->thumbnailLinks, 'Expected "thumbnailLinks" to be a array, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        Assert::string($this->projectName, 'Expected "projectName" to be a string, got %s');
        Assert::string($this->downloadLink, 'Expected "downloadLink" to be a string, got %s');
        Assert::string($this->creationDate, 'Expected "creationDate" to be a string, got %s');
        Assert::string($this->fileCreationDate, 'Expected "fileCreationDate" to be a string, got %s');
    }
}
