<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search;

use Elastica\Aggregation\Terms as TermsAggregation;
use Elastica\Query;
use Froq\AssetBundle\Pimtoday\Action\Search\Filter\BuildDynamicFilters;
use Froq\AssetBundle\Pimtoday\Action\Search\Query\BuildQuerySearch;
use Froq\AssetBundle\Pimtoday\Action\Search\Query\BuildUserOrganizationQuery;
use Froq\AssetBundle\Pimtoday\Action\Search\Sort\BuildSortQuery;
use Froq\AssetBundle\Pimtoday\Controller\Request\SearchRequest;
use Froq\AssetBundle\Pimtoday\Enum\SortNames;
use Froq\PortalBundle\Enum\Elasticsearch\Aggregation;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibMappingManager;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Youwe\PimcoreElasticsearchBundle\Client\ElasticsearchClientInterface;
use Youwe\PimcoreElasticsearchBundle\Model\SearchResult;

final class GetSearchResultSet
{
    public function __construct(
        private readonly ElasticsearchClientInterface $assetLibraryElasticsearchClient,
        private readonly PaginateQuery $paginateQuery,
        private readonly ApplicationLogger $logger,
        private readonly BuildUserOrganizationQuery $buildUserOrganizationQuery,
        private readonly BuildQuerySearch $buildQuerySearch,
        private readonly BuildDynamicFilters $buildDynamicFilters,
        private readonly BuildSortQuery $buildSortQuery,
        private readonly AssetLibMappingManager $mappingManager
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, Organization $organization, #[CurrentUser] User $user): ?SearchResult
    {
        $query = new Query();

        $boolQuery = new Query\BoolQuery();

        ($this->buildUserOrganizationQuery)($query, $boolQuery, $organization);

        ($this->buildQuerySearch)($boolQuery, $query, $searchRequest);

        ($this->buildDynamicFilters)($boolQuery, $query, $searchRequest);

        ($this->buildSortQuery)($query, $searchRequest);

        ($this->paginateQuery)($query, $searchRequest);

        foreach ($this->mappingManager->getFiltersMapping($user) as $fieldId => $data) {
            $fieldType = $data['type'];

            if ($fieldType !== MappingTypes::MAPPING_TYPE_KEYWORD) {
                continue;
            }

            $termsAggregation = new TermsAggregation((string) $fieldId);
            $termsAggregation->setField((string) $fieldId);
            $termsAggregation->setSize(Aggregation::SizeLimit->readable());
            $termsAggregation->setOrder('_term', SortNames::Asc->readable());

            $query->addAggregation($termsAggregation);
        }

        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                return $this->assetLibraryElasticsearchClient->searchElements($query);
            } catch (\Exception $ex) {
                $retryCount++;

                if ($retryCount === $maxRetries) {
                    $this->logger->critical($ex->getMessage(), ['exception' => $ex]);
                }
            }
        }

        return null;
    }
}
