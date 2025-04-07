<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class AssetInfoSectionItem
{
    public function __construct(
        public string $type,
        public string $createdDate,
        public string $fileCreatedDate,
        public int $version,
        /** @var array<int, TagItem> $tags */
        public array $tags,
        /** @var array<int, AssetResourceMetadataItem> $assetResourceMetadata */
        public array $assetResourceMetadata,
    ) {
        Assert::string($this->type, 'Expected "type" to be a string, got %s');
        Assert::string($this->createdDate, 'Expected "createdDate" to be a string, got %s');
        Assert::string($this->fileCreatedDate, 'Expected "fileCreatedDate" to be a string, got %s');
        Assert::integer($this->version, 'Expected "version" to be a int, got %s');
        Assert::isArray($this->tags, 'Expected "tags" to be a array, got %s');
        Assert::isArray($this->assetResourceMetadata, 'Expected "assetResourceMetadata" to be a array, got %s');
    }
}
