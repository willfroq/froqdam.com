<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search\Builder;

use Froq\AssetBundle\Pimtoday\Controller\Request\SearchRequest;
use Froq\AssetBundle\Pimtoday\Enum\FilterTypes;
use Froq\AssetBundle\Pimtoday\Enum\SortNames;
use Froq\AssetBundle\Pimtoday\ValueObject\Search\DateRangeFilter;
use Froq\AssetBundle\Pimtoday\ValueObject\Search\InputFilter;
use Froq\AssetBundle\Pimtoday\ValueObject\Search\MultiselectCheckboxFilter;
use Froq\AssetBundle\Pimtoday\ValueObject\Search\NumberRangeFilter;
use Symfony\Component\HttpFoundation\Request;

final class BuildSearchRequest
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): SearchRequest
    {
        $page = (int) $request->query->get(key: 'page');
        $size = (int) $request->query->get(key: 'size');
        $sortBy = (string) $request->query->get(key: 'sort_by');
        $sortDirection = !empty((string) $request->query->get(key: 'sort_by')) ? (string) $request->query->get(key: 'sort_by') : SortNames::Desc->readable();
        $urlFilters = (array) $request->get(key: 'filters', default: []);

        $hasErrors = false;

        if ($page) {
            $hasErrors = !is_numeric($page);
        }

        if ($size) {
            $hasErrors = !is_numeric($size);
        }

        if ($sortDirection) {
            $hasErrors = !in_array(needle: $sortDirection, haystack: [SortNames::Asc->readable(), SortNames::Desc->readable()]);
        }

        $filterValueObjects = [];

        foreach ($urlFilters as $filterKey => $filterValues) {
            $filterValueObjects[$filterKey] = match ($filterValues['type']) {
                FilterTypes::Keyword->readable() => new MultiselectCheckboxFilter((array) $filterValues),

                FilterTypes::Text->readable() => new InputFilter((string) $filterValues),

                FilterTypes::Date->readable() => new DateRangeFilter(
                    startDate: new \DateTime($filterValues['startDate'] ?? ''),
                    endDate: new \DateTime($filterValues['endDate'] ?? ''),
                ),

                FilterTypes::Integer->readable() => new NumberRangeFilter(
                    min: $filterValues['min'] ?? 0,
                    max: $filterValues['max'] ?? 0,
                ),

                default => throw new \InvalidArgumentException(message: 'Unsupported Filter Type')
            };
        }

        return new SearchRequest(
            query: (string) $request->query->get(key: 'query'),
            page: $page,
            size: $size,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            filters: $urlFilters,
            filterValueObjects: $filterValueObjects,
            hasErrors: $hasErrors,
            hasAggregation: !empty($urlFilters),
            aggregationName: 'customer'
        );
    }
}
