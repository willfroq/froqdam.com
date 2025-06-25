<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action;

use Froq\PortalBundle\ColourLibrary\Action\QueryOption\GetAggregationNamesForUser;
use Froq\PortalBundle\ColourLibrary\Action\QueryOption\GetColumnForUser;
use Froq\PortalBundle\ColourLibrary\Action\QueryOption\GetFilterMappingForUser;
use Froq\PortalBundle\ColourLibrary\Action\QueryOption\GetInitialSidebarFilters;
use Froq\PortalBundle\ColourLibrary\Action\QueryOption\GetSortableFieldNamesForUser;
use Froq\PortalBundle\ColourLibrary\Action\QueryOption\GetSortOptionsForUser;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\ColourGuidelineItem;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Enum\FilterTypes;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Enum\SortNames;
use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Froq\PortalBundle\Opensearch\ValueObject\DateRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\InputFilter;
use Froq\PortalBundle\Opensearch\ValueObject\MultiselectCheckboxFilter;
use Froq\PortalBundle\Opensearch\ValueObject\NumberRangeFilter;
use Pimcore\Model\DataObject\User;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSearchRequest
{
    public function __construct(
        private readonly GetColumnForUser $getColumnForUser,
        private readonly GetFilterMappingForUser $getFilterMappingForUser,
        private readonly GetSortableFieldNamesForUser $getSortableFieldNamesForUser,
        private readonly GetSortOptionsForUser $getSortOptions,
        private readonly GetAggregationNamesForUser $getAggregationNamesForUser,
        private readonly GetInitialSidebarFilters $getInitialSidebarFilters,
    ) {
    }

    /**
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function __invoke(Request $request, #[CurrentUser] User $user): SearchRequest
    {
        $page = $request->query->get(key: 'page');
        $size = $request->query->get(key: 'size');

        $sortableFieldNames = ($this->getSortableFieldNamesForUser)($user);
        $sortBy = $request->query->get(key: 'sort_by');
        $sortDirection = $request->query->get(key: 'sort_direction');

        $sortBy = !empty($sortBy) && in_array($sortBy, $sortableFieldNames, true)
            ? (string) $sortBy
            : 'created_at_timestamp';
        $sortDirection = !empty($sortDirection) ? (string) $sortDirection : SortNames::Desc->readable();

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

        $validFiltersForUser = ($this->getFilterMappingForUser)(IndexNames::ColourGuidelineItem->readable(), $user);

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
                FilterTypes::Keyword->readable() => new MultiselectCheckboxFilter(
                    filterName: $filterKey,
                    selectedOptions: (array) $filterValues
                ),

                FilterTypes::Text->readable() => new InputFilter(
                    filterName: $filterKey,
                    text:  (string) $filterValues
                ),

                FilterTypes::Date->readable() => new DateRangeFilter(
                    filterName: $filterKey,
                    startDate: new \DateTime($filterValues['startDate'] ?? ''),
                    endDate: new \DateTime($filterValues['endDate'] ?? ''),
                ),

                FilterTypes::Integer->readable() => new NumberRangeFilter(
                    filterName: $filterKey,
                    min: $filterValues['min'] ?? 0,
                    max: $filterValues['max'] ?? 0,
                ),

                default => throw new \InvalidArgumentException(message: 'Unsupported Filter Type')
            };
        }

        $sortOptions = ($this->getSortOptions)($sortableFieldNames, $user);

        $selectedSortOption = null;

        foreach ($sortOptions as $sortOption) {
            if ($sortOption->filterName === $sortBy && $sortOption->sortDirection === $sortDirection) {
                $selectedSortOption = $sortOption;

                break;
            }
        }

        $indexName = IndexNames::ColourGuidelineItem->readable();

        $sidebarFilters = ($this->getInitialSidebarFilters)($indexName, $user);
        $aggregationNames = ($this->getAggregationNamesForUser)($indexName, $user);
        $columns = ($this->getColumnForUser)($user, $sortDirection, $sortBy);
        $sortableNames = ($this->getSortableFieldNamesForUser)($user);

        $page = empty($page) ? 1 : $page;

        return new SearchRequest(
            query: (string) $request->query->get(key: 'query'),
            page: (int) $page,
            size: (int) $request->query->get(key: 'size'),
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            filters: $urlFilters,
            filterValueObjects: $filterValueObjects,
            hasErrors: $hasErrors,
            hasAggregation: !empty($aggregationNames),
            aggregationNames: $aggregationNames,
            sidebarFilters: $sidebarFilters,
            columnNames: array_map(fn (Column $column) => $column->filterName, $columns),
            columns: $columns,
            sortableNames: $sortableNames,
            querySource: array_keys(get_class_vars(ColourGuidelineItem::class)),
            sortOptions: $sortOptions,
            selectedSortOption: $selectedSortOption,
            searchIndex: $indexName,
        );
    }
}
