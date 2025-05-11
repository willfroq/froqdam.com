<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class ProductContentDto
{
    public function __construct(
        public readonly string $value,
        public readonly string $unitId,
    ) {
        Assert::string($this->value, 'Expected "value" to be a string, got %s');
        Assert::string($this->unitId, 'Expected "unitId" to be a string, got %s');
    }
}
