<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Aggregation\Nested as NestedAggregation;
use Elastica\Aggregation\Terms as TermsAggregation;
use Elastica\Query;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Pimcore\Model\DataObject\User;

class AssetLibAggregationsManager
{
    public function __construct(private readonly AssetLibMappingManager $mappingManager)
    {
    }

    /**
     * @param Query $query
     * @param User $user
     *
     * @return Query
     */
    public function addAggregations(Query $query, User $user): Query
    {
        foreach ($this->mappingManager->getFiltersMapping($user) as $fieldId => $data) {
            $fieldType = $data['type'];
            if ($fieldType === MappingTypes::MAPPING_TYPE_KEYWORD) {
                $this->buildAggregationForKeywordMapping($query, (string)$fieldId);
            } elseif ($fieldType === MappingTypes::MAPPING_TYPE_NESTED) {
                $this->buildAggregationForNestedMapping($query, $data['properties'], (string)$fieldId);
            }
        }

        return $query;
    }

    private function buildAggregationForKeywordMapping(Query $query, string $fieldId): void
    {
        $termsAggregation = new TermsAggregation($fieldId);
        $termsAggregation->setField($fieldId);
        $termsAggregation->setSize(50);
        $query->addAggregation($termsAggregation);
    }

    /**
     * @param array<int, array<string, mixed>> $fieldProperties
     */
    private function buildAggregationForNestedMapping(Query $query, array $fieldProperties, string $fieldId): void
    {
        foreach ($fieldProperties as $propertyKey => $propertyData) {
            if ($propertyData['type'] === MappingTypes::MAPPING_TYPE_KEYWORD) {
                $nestedAggregation = new NestedAggregation($fieldId, $fieldId);
                $termsAggregation = new TermsAggregation((string) $propertyKey);
                $termsAggregation->setSize(50);
                $termsAggregation->setField(sprintf('%s.%s', $fieldId, $propertyKey));
                $nestedAggregation->addAggregation($termsAggregation);
                $query->addAggregation($nestedAggregation);
                break;
            }
        }
    }
}
