<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchResponse;
use Froq\PortalBundle\Opensearch\Action\GetPaginator;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSearchResponse
{
    public function __construct(
        private readonly BuildColourGuidelineCollection $buildColourGuidelineCollection,
        private readonly GetPaginator $getPaginator,
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

        return new SearchResponse(
            colourGuidelineItems: $colourGuidelineCollection->items,
            totalCount: $colourGuidelineCollection->totalCount,
            aggregationNames: $searchRequest->aggregationNames,
            aggregations: $colourGuidelineCollection->aggregations,
            sidebarFilters: $searchRequest->sidebarFilters,
            paginator: ($this->getPaginator)(
                requestedPage: (int) $searchRequest->page,
                requestedSize: (int) $searchRequest->size,
                totalCount: $colourGuidelineCollection->totalCount,
            ),
            sortOptions: $searchRequest->sortOptions,
            hasSelectedFilters: $hasSelectedFilters,
            selectedSortOption: $searchRequest->selectedSortOption,
            columns: $searchRequest->columns,
            hasMultipleFilterGroups: count((array) $searchRequest->filters) > 1,
        );
    }
}
