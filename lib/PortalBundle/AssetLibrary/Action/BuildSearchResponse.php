<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action;

use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchResponse;
use Froq\PortalBundle\Opensearch\Action\GetPaginator;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSearchResponse
{
    public function __construct(
        private readonly BuildAssetResourceCollection $buildAssetResourceCollection,
        private readonly GetPaginator $getPaginator,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, #[CurrentUser] User $user): SearchResponse
    {
        $assetResourceCollection = ($this->buildAssetResourceCollection)($searchRequest, $user);

        $hasSelectedFilters = false;

        foreach ($assetResourceCollection->aggregationDtos as $aggregation) {
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
            assetResourceItems: $assetResourceCollection->items,
            totalCount: $assetResourceCollection->totalCount,
            aggregationNames: $searchRequest->aggregationNames,
            aggregations: $assetResourceCollection->aggregationDtos,
            sidebarFilters: $searchRequest->sidebarFilters,
            paginator: ($this->getPaginator)(
                requestedPage: (int) $searchRequest->page,
                requestedSize: (int) $searchRequest->size,
                totalCount: $assetResourceCollection->totalCount,
            ),
            sortOptions: $searchRequest->sortOptions,
            hasSelectedFilters: $hasSelectedFilters,
            selectedSortOption: $searchRequest->selectedSortOption,
            columns: $searchRequest->columns,
            hasMultipleFilterGroups: count((array) $searchRequest->filters) > 1,
        );
    }
}
