<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class Column
{
    public function __construct(
        public string $label,

        public string $filterName,

        public string $sortDirection,
    ) {
        Assert::string($this->label, 'Expected "label" to be a string, got %s');
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::string($this->sortDirection, 'Expected "sortDirection" to be a string, got %s');
    }
}
