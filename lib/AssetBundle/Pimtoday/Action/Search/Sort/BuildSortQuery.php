<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search\Sort;

use Elastica\Query;
use Froq\AssetBundle\Pimtoday\Controller\Request\SearchRequest;
use Froq\AssetBundle\Pimtoday\Enum\SortNames;

final class BuildSortQuery
{
    public function __invoke(Query $query, SearchRequest $searchRequest): void
    {
        $sortBy = !empty($searchRequest->sortBy) ? $searchRequest->sortBy : SortNames::CreationDate->readable();
        $sortDirection = !empty($searchRequest->sortDirection) ? $searchRequest->sortDirection : SortNames::Desc->readable();

        $query->setSort([$sortBy => ['order' => $sortDirection]]);
    }
}
