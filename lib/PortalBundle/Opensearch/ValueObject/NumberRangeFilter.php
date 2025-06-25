<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class NumberRangeFilter
{
    public function __construct(
        public string $filterName,

        public readonly float $min,

        public readonly float $max,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::float($this->min, 'Expected "min" to be a float, got %s');
        Assert::float($this->max, 'Expected "max" to be a float, got %s');
    }
}
