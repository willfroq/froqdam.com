<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Elastica\Query;
use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchRequest as AssetSearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest as ColourSearchRequest;

final class PaginateQuery
{
    public function __invoke(Query $query, ColourSearchRequest|AssetSearchRequest $searchRequest): void
    {
        $page = $searchRequest->page ?: 1;
        $size = $searchRequest->size ?: 24;

        $from = ($page - 1) * $size;

        $query->setSize($size)->setFrom($from);
    }
}
