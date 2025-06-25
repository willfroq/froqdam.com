<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class Bucket
{
    public function __construct(
        public string $key,

        public int $docCount,

        public bool $isSelected,
    ) {
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::integer($this->docCount, 'Expected "docCount" to be a int, got %s');
        Assert::boolean($this->isSelected, 'Expected "isSelected" to be a boolean, got %s');
    }
}
