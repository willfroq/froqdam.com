<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Filters;

use Webmozart\Assert\Assert;

final class Date
{
    public function __construct(
        public string $filterName,
        public string $start,
        public string $end,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be string, got %s');
        Assert::string($this->start, 'Expected "start" to be a string, got %s');
        Assert::string($this->end, 'Expected "end" to be a string, got %s');
    }
}
