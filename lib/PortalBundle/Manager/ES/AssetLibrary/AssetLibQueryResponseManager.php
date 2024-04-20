<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Froq\PortalBundle\DTO\AggregationChoiceDto;
use Froq\PortalBundle\DTO\QueryResponseDto;
use Pimcore\Model\DataObject\AssetResource;
use Youwe\PimcoreElasticsearchBundle\Model\SearchResult;

class AssetLibQueryResponseManager
{
    /**
     * @param SearchResult $result
     *
     * @return QueryResponseDto
     */
    public function createQueryResponseDto(SearchResult $result): QueryResponseDto
    {
        $queryResponseDto = new QueryResponseDto();

        $queryResponseDto->setTotalCount($result->getTotalCount());

        $assetResources = [];
        foreach ($result as $resultItem) {
            /** @var AssetResource[] $assetResources */
            $assetResources[] = $resultItem->getElement();
        }
        $queryResponseDto->setObjects($assetResources);

        $aggregationDTOs = [];
        if (!empty($result->getRawElasticsearchResponse()['aggregations'])) {
            $aggregationDTOs = $this->processAggregations($result->getRawElasticsearchResponse()['aggregations']);
        }
        $queryResponseDto->setAggregationDTOs($aggregationDTOs);

        return $queryResponseDto;
    }

    /**
     * @param array<string, mixed> $aggregations
     *
     * @return array<int, array<int|string, mixed>> | array<int|string, array<int|string, mixed>>
     */
    private function processAggregations(array $aggregations): array
    {
        $choices = [];

        foreach ($aggregations as $aggregationKey => $aggregationData) {
            if (!empty($aggregationData['buckets'])) {
                foreach ($aggregationData['buckets'] as $bucket) {
                    $key = $bucket['key'];
                    $docCount = $bucket['doc_count'];
                    $choices[$aggregationKey][] = new AggregationChoiceDto($key, $docCount);
                }
            } elseif (!empty($aggregationData['doc_count'])) {
                $nestedAggregations = $aggregationData;
                unset($nestedAggregations['doc_count']);

                $nestedChoices = $this->processAggregations($nestedAggregations);
                $choices = array_merge($choices, $nestedChoices);
            }
        }

        return $choices;
    }
}
