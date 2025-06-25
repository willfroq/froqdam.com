<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class SidebarFilter
{
    public function __construct(
        public string $filterName,

        public string $label,

        public string $type,

        public ?Aggregation $aggregation,

        public ?DateRangeFilter $dateRangeFilter,

        public ?NumberRangeFilter $numberRangeFilter,

        public ?InputFilter $inputFilter,

        public bool $shouldExpand
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::string($this->label, 'Expected "label" to be a string, got %s');
        Assert::string($this->type, 'Expected "type" to be a string, got %s');
        Assert::nullOrIsInstanceOf($this->aggregation, Aggregation::class, 'Expected "aggregation" to be instance of Aggregation, got %s');
        Assert::nullOrIsInstanceOf($this->dateRangeFilter, DateRangeFilter::class, 'Expected "dateRangeFilter" to be instance of DateRangeFilter, got %s');
        Assert::nullOrIsInstanceOf($this->numberRangeFilter, NumberRangeFilter::class, 'Expected "numberRangeFilter" to be instance of NumberRangeFilter, got %s');
        Assert::nullOrIsInstanceOf($this->inputFilter, InputFilter::class, 'Expected "inputFilter" to be instance of InputFilter, got %s');
        Assert::boolean($this->shouldExpand, 'Expected "shouldExpand" to be a bool, got %s');
    }

    public function __toString()
    {
        return $this->label;
    }
}
