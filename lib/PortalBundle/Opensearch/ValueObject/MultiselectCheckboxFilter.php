<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class MultiselectCheckboxFilter
{
    public function __construct(
        public string $filterName,

        /** @var array<int, bool|float|int|string> $selectedOptions */
        public array $selectedOptions,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::isArray($this->selectedOptions, 'Expected "selectedOptions" to be a array, got %s');
    }
}
