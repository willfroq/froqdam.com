<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\DataTransferObject;

use Froq\PortalBundle\Opensearch\ValueObject\Aggregation;
use Webmozart\Assert\Assert;

final class AssetResourceCollection
{
    public function __construct(
        public int $totalCount,

        /** @var array<int, AssetResourceItem> */
        public array $items,

        /** @var array<int, Aggregation> */
        public array $aggregationDtos,
    ) {
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->items, 'Expected "items" to be an array, got %s');
        Assert::isArray($this->aggregationDtos, 'Expected "aggregationDtos" to be an array, got %s');
    }
}
