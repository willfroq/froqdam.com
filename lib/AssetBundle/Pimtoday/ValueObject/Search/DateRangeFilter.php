<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Search;

use Webmozart\Assert\Assert;

final class DateRangeFilter
{
    public function __construct(
        public \DateTime $startDate,
        public \DateTime $endDate,
    ) {
        Assert::isInstanceOf($this->startDate, \DateTime::class, 'Expected "startDate" to be a string, got %s');
        Assert::isInstanceOf($this->endDate, \DateTime::class, 'Expected "endDate" to be a string, got %s');
    }
}
