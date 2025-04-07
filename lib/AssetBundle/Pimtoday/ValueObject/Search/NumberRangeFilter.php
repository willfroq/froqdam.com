<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Search;

use Webmozart\Assert\Assert;

final class NumberRangeFilter
{
    public function __construct(
        public float $min,
        public float $max,
    ) {
        Assert::float($this->min, 'Expected "min" to be a float, got %s');
        Assert::float($this->max, 'Expected "max" to be a float, got %s');
    }
}
