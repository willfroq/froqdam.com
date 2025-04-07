<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection;

use Webmozart\Assert\Assert;

final class VersionItem
{
    public function __construct(
        public int $id,
        public string $thumbnail,
        public string $filename,
        public string $modificationDate,
        public string $version,
        public string $downloadLink,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->thumbnail, 'Expected "thumbnail" to be an string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be an string, got %s');
        Assert::string($this->modificationDate, 'Expected "modificationDate" to be an string, got %s');
        Assert::string($this->version, 'Expected "version" to be an string, got %s');
        Assert::string($this->downloadLink, 'Expected "downloadLink" to be an string, got %s');
    }
}
