<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search;

use Elastica\Query;
use Froq\AssetBundle\Pimtoday\Controller\Request\SearchRequest;

final class PaginateQuery
{
    public function __invoke(Query $query, SearchRequest $searchRequest): void
    {
        $page = $searchRequest->page ?: 1;
        $size = $searchRequest->size ?: 24;

        $from = ($page - 1) * $size;

        $query->setSize($size)->setFrom($from);
    }
}
