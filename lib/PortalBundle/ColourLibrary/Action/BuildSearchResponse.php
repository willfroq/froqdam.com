<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchResponse;
use Froq\PortalBundle\Opensearch\Action\Aggregation\GetAggregationNames;
use Froq\PortalBundle\Opensearch\Action\GetPaginator;
use Froq\PortalBundle\Opensearch\ValueObject\SortOption;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSearchResponse
{
    public function __construct(
        private readonly BuildColourGuidelineCollection $buildColourGuidelineCollection,
        private readonly GetPaginator $getPaginator,
        private readonly GetAggregationNames $getAggregationNames,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, #[CurrentUser] User $user): SearchResponse
    {
        $colourGuidelineCollection = ($this->buildColourGuidelineCollection)($searchRequest, $user);

        $hasSelectedFilters = false;

        foreach ($colourGuidelineCollection->aggregations as $aggregation) {
            if (empty($aggregation->buckets)) {
                continue;
            }

            foreach ($aggregation->buckets as $bucket) {
                if (!$bucket->isSelected) {
                    continue;
                }

                $hasSelectedFilters = $bucket->isSelected;
            }
        }

        // TODO: This has to be dynamic later. Admin should be able to configure which field a user can sort, query, aggregate, search, filter etc.
        $sortOptions = [
            new SortOption(label: 'Name', filterName: 'name', sortDirection: 'asc'),
            new SortOption(label: 'Name', filterName: 'name', sortDirection: 'desc'),
            new SortOption(label: 'Newest', filterName: 'created_at_timestamp', sortDirection: 'desc'),
            new SortOption(label: 'Oldest', filterName: 'created_at_timestamp', sortDirection: 'asc'),
        ];

        $selectedSortOption = null;

        foreach ($sortOptions as $sortOption) {
            if ($sortOption->filterName === $searchRequest->sortBy && $sortOption->sortDirection === $searchRequest->sortDirection) {
                $selectedSortOption = $sortOption;
                break;
            }
        }

        return new SearchResponse(
            colourGuidelineItems: $colourGuidelineCollection->items,
            totalCount: $colourGuidelineCollection->totalCount,
            aggregationNames: ($this->getAggregationNames)($user),
            aggregations: $colourGuidelineCollection->aggregations,
            paginator: ($this->getPaginator)(
                requestedPage: (int) $searchRequest->page,
                requestedSize: (int) $searchRequest->size,
                totalCount: $colourGuidelineCollection->totalCount,
            ),
            sortOptions: $sortOptions,
            hasSelectedFilters: $hasSelectedFilters,
            selectedSortOption: $selectedSortOption
        );
    }
}
