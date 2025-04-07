<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Filters;

use Webmozart\Assert\Assert;

final class MulticheckboxItem
{
    public function __construct(
        public string $label,
        public string $filterName,
        public int $count,
    ) {
        Assert::string($this->label, 'Expected "label" to be a string, got %s');
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::integer($this->count, 'Expected "count" to be a int, got %s');
    }
}
