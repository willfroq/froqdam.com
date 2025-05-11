<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Elastica\Query;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;

final class PaginateQuery
{
    public function __invoke(Query $query, SearchRequest $searchRequest): void
    {
        $page = $searchRequest->page ?: 1;
        $size = $searchRequest->size ?: 12;

        $from = ($page - 1) * $size;

        $query->setSize($size)->setFrom($from);
    }
}
