<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Search;

use Webmozart\Assert\Assert;

final class MultiselectCheckboxFilter
{
    public function __construct(
        /** @var array<int, bool|float|int|string> $selectedOptions */
        public array $selectedOptions,
    ) {
        Assert::isArray($this->selectedOptions, 'Expected "selectedOptions" to be a array, got %s');
    }
}
