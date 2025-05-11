<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Elastica\Query;
use Elastica\ResultSet;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\Aggregation\BuildAggregation;
use Froq\PortalBundle\Opensearch\Action\Filter\BuildDynamicFilters;
use Froq\PortalBundle\Opensearch\Action\Query\BuildQuerySearch;
use Froq\PortalBundle\Opensearch\Action\Query\BuildQuerySource;
use Froq\PortalBundle\Opensearch\Action\Query\BuildUserOrganizationQuery;
use Froq\PortalBundle\Opensearch\Action\Sort\BuildSortQuery;
use JoliCode\Elastically\Client;
use Pimcore\Model\DataObject\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetSearchResultSet
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        private readonly BuildUserOrganizationQuery $buildUserOrganizationQuery,
        private readonly BuildQuerySource $buildQuerySource,
        private readonly BuildQuerySearch $buildQuerySearch,
        private readonly BuildDynamicFilters $buildDynamicFilters,
        private readonly BuildSortQuery $buildSortQuery,
        private readonly BuildAggregation $buildAggregation,
        private readonly PaginateQuery $paginateQuery,
    ) {
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, #[CurrentUser] User $user, string $indexName): ?ResultSet
    {
        $query = new Query();

        $boolQuery = new Query\BoolQuery();

        ($this->buildUserOrganizationQuery)($query, $boolQuery, $searchRequest, $user);

        ($this->buildQuerySource)($query, $searchRequest, $user);
        ($this->buildQuerySearch)($boolQuery, $query, $searchRequest, $user);
        ($this->buildDynamicFilters)($boolQuery, $query, $searchRequest);
        ($this->buildSortQuery)($query, $searchRequest, $user);
        ($this->paginateQuery)($query, $searchRequest);

        ($this->buildAggregation)($query, $searchRequest, $user);

        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                return $this->client->getIndex(name: $indexName)->search($query);
            } catch (\Exception $ex) {
                ++$retryCount;

                if ($retryCount === $maxRetries) {
                    $this->logger->critical($ex->getMessage(), ['exception' => $ex]);
                }
            }
        }

        return null;
    }
}
