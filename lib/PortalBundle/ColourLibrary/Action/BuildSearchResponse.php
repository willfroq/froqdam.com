<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchResponse;
use Froq\PortalBundle\Opensearch\Action\Aggregation\GetAggregationNames;
use Froq\PortalBundle\Opensearch\Action\GetPaginator;
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

        return new SearchResponse(
            colourGuidelineItems: $colourGuidelineCollection->items,
            totalCount: $colourGuidelineCollection->totalCount,
            aggregationNames: ($this->getAggregationNames)($user),
            aggregations: $colourGuidelineCollection->aggregations,
            paginator: ($this->getPaginator)(
                requestedPage: (int)$searchRequest->page,
                requestedSize: (int)$searchRequest->size,
                totalCount: $colourGuidelineCollection->totalCount,
            ),
        );
    }
}
