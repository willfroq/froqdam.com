<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Filters;

use Webmozart\Assert\Assert;

final class Input
{
    public function __construct(
        public string $filterName,
        public string $label,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::string($this->label, 'Expected "label" to be a string, got %s');
    }
}
