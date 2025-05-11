<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\Aggregation;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\ColourGuidelineCollection;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\ColourGuidelineItem;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\GetSearchResultSet;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use JoliCode\Elastically\Result;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildColourGuidelineCollection
{
    public function __construct(private readonly GetSearchResultSet $getSearchResultSet)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, #[CurrentUser] User $user): ColourGuidelineCollection
    {
        $resultSet = ($this->getSearchResultSet)($searchRequest, $user, IndexNames::ColourGuidelineItem->readable());

        $colourGuidelineItems = [];

        foreach ($resultSet?->getResults() ?? [] as $result) {
            if (!($result instanceof Result)) {
                continue;
            }

            $item = $result->getModel();

            if (!($item instanceof ColourGuidelineItem)) {
                continue;
            }

            $colourGuidelineItems[] = $item;
        }

        $aggregations = [];

        $responseAggregations = $resultSet?->getAggregations() ?? [];

        if (!empty($responseAggregations) && $searchRequest->hasAggregation) {
            foreach ($responseAggregations as $aggregationName => $aggregation) {
                if (!in_array(needle: $aggregationName, haystack: $searchRequest->aggregationNames)) {
                    continue;
                }

                $aggregations[] = new Aggregation(
                    fieldName: (string) $aggregationName,
                    hasError: (bool) $aggregation['doc_count_error_upper_bound'],
                    sumOfDocCount: (int) $aggregation['sum_other_doc_count'],
                    buckets: $aggregation['buckets'] ?? []
                );
            }
        }

        return new ColourGuidelineCollection(
            totalCount: (int) $resultSet?->getTotalHits(),
            items: $colourGuidelineItems,
            aggregations: $aggregations
        );
    }
}
