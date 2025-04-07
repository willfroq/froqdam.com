<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Filters;

use Webmozart\Assert\Assert;

final class Range
{
    public function __construct(
        public string $filterName,
        public int $min,
        public int $max,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be string, got %s');
        Assert::integer($this->min, 'Expected "min" to be integer, got %s');
        Assert::integer($this->max, 'Expected "max" to be integer, got %s');
    }
}
