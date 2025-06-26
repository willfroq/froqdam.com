<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\ColourGuidelineCollection;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\ColourGuidelineItem;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\GetSearchResultSet;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\ValueObject\Aggregation;
use Froq\PortalBundle\Opensearch\ValueObject\Bucket;
use Froq\PortalBundle\Opensearch\ValueObject\DateRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\InputFilter;
use Froq\PortalBundle\Opensearch\ValueObject\NumberRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use JoliCode\Elastically\Result;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildColourGuidelineCollection
{
    public function __construct(
        private readonly GetSearchResultSet $getSearchResultSet,
    ) {
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
            foreach (array_reverse($responseAggregations, true) as $fieldName => $globalAggregation) {
                if (!in_array(needle: $fieldName, haystack: $searchRequest->aggregationNames)) {
                    continue;
                }

                $filteredAggregation = $globalAggregation["filtered_{$fieldName}"] ?? [];

                $totalDocCount = $filteredAggregation['doc_count'] ?? 0;

                $facetAggregation = $filteredAggregation["facet_{$fieldName}"] ?? [];

                $buckets = $facetAggregation['buckets'] ?? [];

                $sidebarFilter = current(array_filter($searchRequest->sidebarFilters, fn (SidebarFilter $sidebarFilter) => $sidebarFilter->filterName === $fieldName));

                if (!($sidebarFilter instanceof SidebarFilter)) {
                    continue;
                }

                $label = !empty($sidebarFilter->label) ? $sidebarFilter->label : ucfirst(str_replace('_', ' ', $fieldName));

                $aggregation = new Aggregation(
                    label: (string) empty($label) ? ucfirst(str_replace('_', ' ', $fieldName)) : $label,
                    filterName: (string) $fieldName,
                    hasError: (bool) $facetAggregation['doc_count_error_upper_bound'],
                    sumOfDocCount: (int) $facetAggregation['sum_other_doc_count'],
                    shouldExpand: in_array(needle: $fieldName, haystack: array_keys((array) $searchRequest->filters)),
                    totalDocCount: (int) $totalDocCount,
                    buckets: array_map(
                        fn (mixed $item) =>  new Bucket(
                            key: (string) $item['key'],
                            docCount: (int) $item['doc_count'],
                            isSelected: (
                                function () use ($searchRequest, $fieldName, $item) {
                                    $key = $item['key'] ?? '';

                                    foreach ($searchRequest->filters ?? [] as $index => $filterNames) {
                                        if ($index === $fieldName && in_array(needle: $key, haystack: (array) $filterNames)) {
                                            return true;
                                        }
                                    }

                                    return false;
                                }
                            )(),
                        ), $buckets
                    )
                );

                $sidebarFilter->label = $label;
                $sidebarFilter->shouldExpand = $aggregation->shouldExpand;
                $sidebarFilter->aggregation = $aggregation;

                $aggregations[] = $aggregation;
            }
        }

        foreach ($searchRequest->sidebarFilters as $sidebarFilter) {
            if ($sidebarFilter->type === 'keyword') {
                continue;
            }

            $requestFilter = (array) $searchRequest->filterValueObjects;

            if ($sidebarFilter->type === 'text') {
                array_walk(
                    $requestFilter,
                    function (mixed $inputFilter, string $filterName) use ($sidebarFilter, $searchRequest) {
                        $requestFilter = $searchRequest->filterValueObjects[$filterName] ?? null;

                        $inputFilterDto = $requestFilter instanceof InputFilter ? $requestFilter : null;

                        if ($sidebarFilter->filterName === $inputFilterDto?->filterName) {
                            $sidebarFilter->inputFilter = $inputFilterDto;
                            $sidebarFilter->shouldExpand = true;
                        }
                    },
                );
            }

            if ($sidebarFilter->type === 'date') {
                array_walk(
                    $requestFilter,
                    function (mixed $dateRangeFilter, string $filterName) use ($sidebarFilter, $searchRequest) {
                        $requestFilter = $searchRequest->filterValueObjects[$filterName] ?? null;

                        $dateRangeFilterDto = $requestFilter instanceof DateRangeFilter ? $requestFilter : null;

                        if ($sidebarFilter->filterName === $dateRangeFilterDto?->filterName) {
                            $sidebarFilter->dateRangeFilter = $dateRangeFilterDto;
                            $sidebarFilter->shouldExpand = true;
                        }
                    }
                );
            }

            if ($sidebarFilter->type === 'integer') {
                array_walk(
                    $requestFilter,
                    function (mixed $numberRangeFilter, string $filterName) use ($sidebarFilter, $searchRequest) {
                        $requestFilter = $searchRequest->filterValueObjects[$filterName] ?? null;

                        $numberRangeFilterDto = $requestFilter instanceof NumberRangeFilter ? $requestFilter : null;

                        if ($sidebarFilter->filterName === $numberRangeFilterDto?->filterName) {
                            $sidebarFilter->numberRangeFilter = $numberRangeFilterDto;
                            $sidebarFilter->shouldExpand = true;
                        }
                    }
                );
            }

            if (!empty($sidebarFilter->label)) {
                continue;
            }

            $sidebarFilter->label = ucfirst(str_replace('_', ' ', $sidebarFilter->filterName));
        }

        return new ColourGuidelineCollection(
            totalCount: (int) $resultSet?->getTotalHits(),
            items: $colourGuidelineItems,
            aggregations: $aggregations
        );
    }
}
