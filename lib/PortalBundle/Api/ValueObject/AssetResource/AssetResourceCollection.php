<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResource;

use Webmozart\Assert\Assert;

final class AssetResourceCollection
{
    public function __construct(
        public readonly int $totalCount,

        /** @var array<int, AssetResourceItem> */
        public readonly array $items,
    ) {
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->items, 'Expected "items" to be an array, got %s');
    }
}
