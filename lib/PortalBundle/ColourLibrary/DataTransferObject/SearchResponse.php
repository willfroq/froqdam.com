<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Froq\PortalBundle\Opensearch\ValueObject\Aggregation;

final class SearchResponse
{
    public function __construct(
        /** @var array<int, ColourGuidelineItem> */
        public array $colourGuidelineItems,

        public int $totalCount,

        /** @var array<int, string> */
        public array $aggregationNames,

        /** @var array<int, Aggregation> */
        public array $aggregations,

        /** @var array<string, int> */
        public array $paginator,

        /** @var array<int, string> */
        public array $sortOptions,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'colourGuidelineItems' => $this->colourGuidelineItems,
            'totalCount' => $this->totalCount,
            'aggregationNames' => $this->aggregationNames,
            'aggregations' => $this->aggregations,
            'paginator' => $this->paginator,
            'sortOptions' => $this->sortOptions
        ];
    }
}
