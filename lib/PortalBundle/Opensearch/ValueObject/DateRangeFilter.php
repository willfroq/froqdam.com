<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class DateRangeFilter
{
    public function __construct(
        public string $filterName,

        public readonly \DateTime $startDate,

        public readonly \DateTime $endDate,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::isInstanceOf($this->startDate, \DateTime::class, 'Expected "startDate" to be a string, got %s');
        Assert::isInstanceOf($this->endDate, \DateTime::class, 'Expected "endDate" to be a string, got %s');
    }
}
