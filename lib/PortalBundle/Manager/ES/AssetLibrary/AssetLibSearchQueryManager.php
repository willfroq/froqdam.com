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
    public function applySearch(BoolQuery $boolQuery, ?LibraryFormDto $formDto, bool &$sortByRelevance): BoolQuery
    {
        $searchTerm = (string) $formDto?->getQuery();

        if (!$searchTerm) {
            return $boolQuery;
        }

        $queryStringQuery = new QueryString();

        $isFilename = preg_match('/^[a-zA-Z0-9._-]+$/', $searchTerm) && preg_match('/\./', $searchTerm);

        if ($isFilename && !preg_match('/\b(AND|OR|NOT)\b/', $searchTerm)) {
            $queryStringQuery = new QueryString("file_name:$searchTerm");

            $boolQuery->addMust($queryStringQuery);

            return $boolQuery;
        }

        if (!preg_match('/\b(AND|OR|NOT)\b/', $searchTerm)) {
            $searchTerm = preg_replace('/\s+/', ' AND ', $searchTerm);
        }

        $queryStringQuery->setQuery((string) $searchTerm);

        $boolQuery->addMust($queryStringQuery);

        $sortByRelevance = true;

        return $boolQuery;
    }
}
