<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\Aggregation\GetAggregationNames;
use Froq\PortalBundle\Opensearch\Action\Filter\GetFilterMappingForUser;
use Froq\PortalBundle\Opensearch\Enum\FilterTypes;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Enum\SortNames;
use Froq\PortalBundle\Opensearch\ValueObject\DateRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\InputFilter;
use Froq\PortalBundle\Opensearch\ValueObject\MultiselectCheckboxFilter;
use Froq\PortalBundle\Opensearch\ValueObject\NumberRangeFilter;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSearchRequest
{
    public function __construct(
        private readonly GetFilterMappingForUser $getFilterMappingForUser,
        private readonly GetAggregationNames $getAggregationNames,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, #[CurrentUser] User $user): SearchRequest
    {
        $page = $request->query->get(key: 'page');
        $size = $request->query->get(key: 'size');
        $sortBy = (string) $request->query->get(key: 'sort_by');
        $sortDirection = (string) $request->query->get(key: 'sort_direction');
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

        $validFiltersForUser = ($this->getFilterMappingForUser)($user, IndexNames::ColourGuidelineItem->readable());

        foreach ($urlFilters as $filterKey => $filterValues) {
            if (!isset($validFiltersForUser[$filterKey])) {
                continue;
            }

            if (!isset($validFiltersForUser[$filterKey]['type'])) {
                continue;
            }

            if ($validFiltersForUser[$filterKey]['type'] === 'text') {
                $hasErrors = !is_string($filterValues);
            }

            $filterValueObjects[$filterKey] = match ($validFiltersForUser[$filterKey]['type']) {
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

        $aggregationNames = ($this->getAggregationNames)($user);

        return new SearchRequest(
            query: (string) $request->query->get(key: 'query'),
            page: (int) $request->query->get(key: 'page'),
            size: (int) $request->query->get(key: 'size'),
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            filters: $urlFilters,
            filterValueObjects: $filterValueObjects,
            hasErrors: $hasErrors,
            hasAggregation: !empty($aggregationNames),
            aggregationNames: $aggregationNames,
            searchIndex: IndexNames::ColourGuidelineItem->readable(),
        );
    }
}
