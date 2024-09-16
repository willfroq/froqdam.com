<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\DTO\QueryResponseDto;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\User;
use Youwe\PimcoreElasticsearchBundle\Client\ElasticsearchClientInterface;

class AssetLibQueryBuilderManager
{
    public function __construct(
        private readonly ElasticsearchClientInterface $assetLibraryElasticsearchClient,
        private readonly AssetLibSearchQueryManager $searchQueryManager,
        private readonly AssetLibSortManager $sortManager,
        private readonly AssetLibFilterManager $filterManager,
        private readonly AssetLibAggregationsManager $aggregationsManager,
        private readonly AssetLibQueryResponseManager $queryResponseManager,
        private readonly ApplicationLogger $logger)
    {
    }

    /**
     * @param User $user
     * @param LibraryFormDto|null $formDto
     *
     * @return QueryResponseDto|null
     */
    public function search(User $user, ?LibraryFormDto $formDto = null): ?QueryResponseDto
    {
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                $boolQuery = new Query\BoolQuery();
                $query = new Query();
                $query->setQuery($boolQuery);
                $query->setParam('track_total_hits', true);

                $sortByRelevance = false;

                $this->searchQueryManager->applySearch($boolQuery, $formDto, $sortByRelevance);

                $this->filterManager->filter($boolQuery, $user, $formDto, $sortByRelevance);

                $this->sortManager->sort($query, $user, $formDto, $sortByRelevance);

                $this->paginate($query, $formDto);

                $this->aggregationsManager->addAggregations($query, $user);

                $result = $this->assetLibraryElasticsearchClient->searchElements($query);

                return $this->queryResponseManager->createQueryResponseDto($result);
            } catch (\Exception $ex) {
                $retryCount++;

                if ($retryCount === $maxRetries) {
                    // TODO:: It's better throw error after logging, because it suppresses all errors which is not good, Move error handling outside of class
                    $this->logger->critical($ex->getMessage(), [
                        'exception' => $ex
                    ]);
                }
            }
        }

        return null;
    }

    private function paginate(Query $query, ?LibraryFormDto $formDto = null): void
    {
        $page = $formDto?->getPage() ?: 1;
        $size = $formDto?->getSize() ?: 12;
        $from = ((int)$page - 1) * (int)$size;

        $query
            ->setSize((int)$size)
            ->setFrom($from);
    }
}
