<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class ProjectExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $key,
        public readonly string $path,
        public readonly string $code,
        public readonly string $name,
        public readonly string $froqName,
        public readonly string $pimProjectNumber,
        public readonly string $froqProjectNumber,
        public readonly string $customerProjectNumber,
        public readonly string $description,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->code, 'Expected "code" to be a string, got %s');
        Assert::string($this->froqName, 'Expected "froqName" to be a string, got %s');
        Assert::string($this->pimProjectNumber, 'Expected "pimProjectNumber" to be a string, got %s');
        Assert::string($this->froqProjectNumber, 'Expected "froqProjectNumber" to be a string, got %s');
        Assert::string($this->customerProjectNumber, 'Expected "customerProjectNumber" to be a string, got %s');
        Assert::string($this->description, 'Expected "description" to be a string, got %s');
    }
}
