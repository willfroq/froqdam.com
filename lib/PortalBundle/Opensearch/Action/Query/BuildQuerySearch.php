<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildQuerySearch
{
    /**
     * @throws \Exception
     */
    public function __invoke(BoolQuery $boolQuery, Query $query, SearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        $searchTerm = $searchRequest->query;

        if (!$searchTerm) {
            return;
        }

        $queryString = new QueryString();

        if (!preg_match('/\b(AND|OR|NOT)\b/', $searchTerm)) {
            $searchTerm = preg_replace('/\s+/', ' AND ', $searchTerm);
        }

        $queryString->setQuery((string) $searchTerm);
        $boolQuery->addMust($queryString);

        $query->setQuery($boolQuery);
    }
}
