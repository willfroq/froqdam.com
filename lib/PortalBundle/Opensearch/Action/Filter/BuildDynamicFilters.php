<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Filter;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use Elastica\Query\Range;
use Elastica\Query\Terms;
use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchRequest as AssetSearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest as ColourSearchRequest;
use Froq\PortalBundle\Opensearch\ValueObject\DateRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\InputFilter;
use Froq\PortalBundle\Opensearch\ValueObject\MultiselectCheckboxFilter;
use Froq\PortalBundle\Opensearch\ValueObject\NumberRangeFilter;

final class BuildDynamicFilters
{
    public function __invoke(BoolQuery $boolQuery, Query $query, ColourSearchRequest|AssetSearchRequest $searchRequest): void
    {
        if (empty($searchRequest->filterValueObjects)) {
            return;
        }

        foreach ($searchRequest->filterValueObjects as $filterName => $filterValueObject) {
            match (true) {
                $filterValueObject instanceof InputFilter && $filterValueObject->text => (
                    function () use ($filterValueObject, $boolQuery, $filterName) {
                        $searchTerm = $filterValueObject->text;

                        $queryStringQuery = new QueryString($searchTerm);
                        $queryStringQuery->setDefaultField((string) $filterName);
                        $queryStringQuery->setDefaultOperator('AND');

                        $boolQuery->addFilter($queryStringQuery);
                    }
                )(),
                $filterValueObject instanceof DateRangeFilter => (
                    function () use ($filterValueObject, $boolQuery, $filterName) {
                        $options['gte'] = $filterValueObject->startDate->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
                        $options['lte'] = $filterValueObject->endDate->modify('+1 day')->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();

                        $boolQuery->addFilter(new Range((string) $filterName, $options));
                    }
                )(),
                $filterValueObject instanceof MultiselectCheckboxFilter => (
                    fn () => $boolQuery->addFilter(new Terms($filterName, $filterValueObject->selectedOptions))
                )(),
                $filterValueObject instanceof NumberRangeFilter => (
                    function () use ($filterValueObject, $boolQuery, $filterName) {
                        $options['gte'] = $filterValueObject->min;
                        $options['lte'] = $filterValueObject->max;

                        $boolQuery->addFilter(new Range((string) $filterName, $options));
                    }
                )(),

                default => throw new \InvalidArgumentException(message: 'Unsupported Filter Value Object'),
            };
        }

        $query->setQuery($boolQuery);
    }
}
