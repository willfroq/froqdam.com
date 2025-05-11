<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class BlockElementDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $data,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->type, 'Expected "type" to be a string, got %s');
        Assert::string($this->data, 'Expected "data" to be a string, got %s');
    }
}
