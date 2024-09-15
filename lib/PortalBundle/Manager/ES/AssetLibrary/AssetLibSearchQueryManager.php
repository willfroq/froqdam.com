<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;

class AssetLibSearchQueryManager
{
    /**
     * @param BoolQuery $boolQuery
     * @param LibraryFormDto|null $formDto
     *
     * @return BoolQuery
     */
    public function applySearch(BoolQuery $boolQuery, ?LibraryFormDto $formDto = null): BoolQuery
    {
        $searchTerm = (string) $formDto?->getQuery();

        if (!$searchTerm) {
            return $boolQuery;
        }

        $queryString = new QueryString();

        if (!preg_match('/\b(AND|OR|NOT)\b/', $searchTerm)) {
            $searchTerm = preg_replace('/\s+/', ' AND ', $searchTerm);
        }

        $queryString->setQuery($searchTerm);
        $boolQuery->addMust($queryString);

        return $boolQuery;
    }
}
