<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class AssetDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $filename,
        public readonly string $path,
        public readonly string $fullPath,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->fullPath, 'Expected "fullPath" to be a string, got %s');
    }
}
