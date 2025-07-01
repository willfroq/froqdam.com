<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Aggregation;

use Elastica\Aggregation\Filter;
use Elastica\Aggregation\GlobalAggregation;
use Elastica\Aggregation\Terms as TermsAggregation;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms;
use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchRequest as AssetSearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest as ColourSearchRequest;
use Froq\PortalBundle\Opensearch\ValueObject\MultiselectCheckboxFilter;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildAggregation
{
    /**
     * @throws \Exception
     */
    public function __invoke(Query $query, ColourSearchRequest|AssetSearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        if (empty($searchRequest->aggregationNames)) {
            return;
        }

        foreach ($searchRequest->aggregationNames as $aggregationName) {
            $termsAggregation = new TermsAggregation("facet_$aggregationName");
            $termsAggregation->setField($aggregationName);
            $termsAggregation->setSize(100);
            $termsAggregation->setOrder('_term', 'asc');

            $selfExcludingQuery = new BoolQuery();

            foreach ($searchRequest->filterValueObjects ?? [] as $filterName => $filterValueObject) {
                if (!($filterValueObject instanceof MultiselectCheckboxFilter)) {
                    continue;
                }

                if (trim((string) $filterName) === trim($aggregationName)) {
                    continue;
                }

                $selfExcludingQuery->addFilter(new Terms($filterValueObject->filterName, $filterValueObject->selectedOptions));
            }

            $globalAggregation = new GlobalAggregation($aggregationName);

            $selfExcludingQuery->addFilter(
                new Terms(
                    'organization_id',
                    array_filter(array_map(callback: fn (Organization $organization) => $organization->getId(), array: $user->getOrganizations()))
                )
            );

            $filterAggregation = new Filter("filtered_{$aggregationName}", $selfExcludingQuery);
            $filterAggregation->addAggregation($termsAggregation);

            $globalAggregation->addAggregation($filterAggregation);

            $query->addAggregation($globalAggregation);
        }
    }
}
