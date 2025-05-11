<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Factory;

use Froq\PortalBundle\Opensearch\Action\GetColourGuidelineItemFilterNames;
use Froq\PortalBundle\Opensearch\Contract\GetFilterNamesInterface;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;

final class GetItemNamesFactory
{
    public function __construct(
        private readonly GetColourGuidelineItemFilterNames $getColourGuidelineItemFilterNames
    ) {
    }

    public function create(string $indexName): GetFilterNamesInterface
    {
        return match ($indexName) {
            IndexNames::ColourGuidelineItem->readable() => $this->getColourGuidelineItemFilterNames,

            default => throw new \InvalidArgumentException("Unknown index name: $indexName"),
        };
    }
}
