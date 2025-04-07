<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Filters;

use Webmozart\Assert\Assert;

final class MulticheckboxCollection
{
    public function __construct(
        public string $filterName,
        public int $totalCount,

        /** @var array<int, MulticheckboxItem> */
        public array $items,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be string, got %s');
        Assert::integer($this->totalCount, 'Expected "totalCount" to int, got %s');
        Assert::isArray($this->items, 'Expected "items" to be an array, got %s');
    }
}
