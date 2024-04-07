<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection;

use Webmozart\Assert\Assert;

final class LinkedCollection
{
    public function __construct(
        public readonly int $totalCount,

        /** @var array<int, LinkedItem> */
        public readonly array $items,
    ) {
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->items, 'Expected "projects" to be an array, got %s');
    }
}
