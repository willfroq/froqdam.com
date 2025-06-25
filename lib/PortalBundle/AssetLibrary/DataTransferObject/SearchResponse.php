<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\DataTransferObject;

use Froq\PortalBundle\Opensearch\ValueObject\Aggregation;
use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Froq\PortalBundle\Opensearch\ValueObject\SortOption;

final class SearchResponse
{
    public function __construct(
        /** @var array<int, AssetResourceItem> */
        public array $assetResourceItems,

        public int $totalCount,

        /** @var array<int, string> */
        public array $aggregationNames,

        /** @var array<int, Aggregation> */
        public array $aggregations,

        /** @var array<int, SidebarFilter> */
        public array $sidebarFilters,

        /** @var array<string, int> */
        public array $paginator,

        /** @var array<int, SortOption> */
        public array $sortOptions,

        public bool $hasSelectedFilters,

        public ?SortOption $selectedSortOption,

        /** @var array<int, Column> */
        public array $columns,

        public bool $hasMultipleFilterGroups
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'assetResourceItems' => $this->assetResourceItems,
            'totalCount' => $this->totalCount,
            'aggregationNames' => $this->aggregationNames,
            'aggregations' => $this->aggregations,
            'sidebarFilters' => $this->sidebarFilters,
            'paginator' => $this->paginator,
            'sortOptions' => $this->sortOptions,
            'hasSelectedFilters' => $this->hasSelectedFilters,
            'selectedSortOption' => $this->selectedSortOption,
            'columns' => $this->columns,
            'hasMultipleFilterGroups' => $this->hasMultipleFilterGroups,
        ];
    }
}
