<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class OrganizationExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly int $code,
        public readonly ?int $mainContactId,
        public readonly string $name,
        public readonly string $key,
        public readonly string $path,
        public readonly string $objectFolder,
        public readonly string $assetFolder,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::numeric($this->code, 'Expected "code" to be a numeric, got %s');
        Assert::nullOrInteger($this->mainContactId, 'Expected "mainContactId" to be a numeric, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->objectFolder, 'Expected "objectFolder" to be a string, got %s');
        Assert::string($this->assetFolder, 'Expected "assetFolder" to be a string, got %s');
    }
}
