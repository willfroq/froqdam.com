<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class Aggregation
{
    public function __construct(
        public string $fieldName,

        public bool $hasError,

        public int $sumOfDocCount,

        public bool $shouldExpand,

        /** @var array<int, Bucket> */
        public array $buckets,
    ) {
        Assert::string($this->fieldName, 'Expected "fieldName" to be a string, got %s');
        Assert::boolean($this->hasError, 'Expected "hasError" to be a boolean, got %s');
        Assert::integer($this->sumOfDocCount, 'Expected "sumOfDocCount" to be a int, got %s');
        Assert::boolean($this->shouldExpand, 'Expected "shouldExpand" to be a boolean, got %s');
        Assert::isArray($this->buckets, 'Expected "buckets" to be a array, got %s');
    }
}
