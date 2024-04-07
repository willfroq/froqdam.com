<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection;

use Webmozart\Assert\Assert;

final class VersionItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $thumbnail,
        public readonly string $filename,
        public readonly string $modificationDate,
        public readonly string $version,
        public readonly string $linkToItem,
        public readonly string $downloadLink,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->thumbnail, 'Expected "thumbnail" to be an string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be an string, got %s');
        Assert::string($this->modificationDate, 'Expected "modificationDate" to be an string, got %s');
        Assert::string($this->version, 'Expected "version" to be an string, got %s');
        Assert::string($this->linkToItem, 'Expected "linkToItem" to be an string, got %s');
        Assert::string($this->downloadLink, 'Expected "downloadLink" to be an string, got %s');
    }
}
