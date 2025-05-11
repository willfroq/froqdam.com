<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class AssetResourceFolderExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $key,
        public readonly string $path,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
    }
}
