<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetInitialSidebarFilters
{
    /**
     * @throws \Exception
     *
     * @return array<int, SidebarFilter>
     */
    public function __invoke(string $indexName, #[CurrentUser] User $user): array
    {
        return [
            new SidebarFilter(
                filterName: 'markets',
                label: 'Markets',
                type: 'keyword',
                aggregation: null,
                dateRangeFilter: null,
                numberRangeFilter: null,
                inputFilter: null,
                shouldExpand: false
            ),
            new SidebarFilter(
                filterName: 'brands',
                label: 'Brands',
                type: 'keyword',
                aggregation: null,
                dateRangeFilter: null,
                numberRangeFilter: null,
                inputFilter: null,
                shouldExpand: false
            ),
            new SidebarFilter(
                filterName: 'substrates',
                label: 'Substrates',
                type: 'keyword',
                aggregation: null,
                dateRangeFilter: null,
                numberRangeFilter: null,
                inputFilter: null,
                shouldExpand: false
            ),
            new SidebarFilter(
                filterName: 'printing_technique',
                label: 'Printing Technique',
                type: 'keyword',
                aggregation: null,
                dateRangeFilter: null,
                numberRangeFilter: null,
                inputFilter: null,
                shouldExpand: false
            ),

            new SidebarFilter(
                filterName: 'mediums',
                label: 'Mediums',
                type: 'keyword',
                aggregation: null,
                dateRangeFilter: null,
                numberRangeFilter: null,
                inputFilter: null,
                shouldExpand: false
            ),
        ];
    }
}
