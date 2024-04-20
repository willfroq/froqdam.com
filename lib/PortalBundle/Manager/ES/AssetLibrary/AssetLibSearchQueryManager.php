<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Nested as NestedQuery;
use Elastica\Query\Wildcard;
use Elastica\Query\QueryString;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Pimcore\Model\DataObject\User;

class AssetLibSearchQueryManager
{
    public function __construct(private readonly AssetLibMappingManager $mappingManager)
    {
    }

    /**
     * @param Query $query
     * @param User $user
     * @param LibraryFormDto|null $libraryFormDto
     *
     * @return Query
     */
    public function buildQuerySource(Query $query, User $user, ?LibraryFormDto $libraryFormDto = null): Query
    {
        if (!$libraryFormDto?->getQuery()) {
            return $query;
        }

        $source = [];

        foreach ($this->mappingManager->getFiltersMapping($user) as $fieldId => $data) {
            $fieldType = $data['type'];
            if (($fieldType === MappingTypes::MAPPING_TYPE_KEYWORD) || ($fieldType === MappingTypes::MAPPING_TYPE_TEXT)) {
                $source[] = $fieldId;
            } elseif ($fieldType === MappingTypes::MAPPING_TYPE_NESTED) {
                $fieldProperties = $data['properties'];
                foreach ($fieldProperties as $propertyKey => $propertyData) {
                    if ($propertyData['type'] === MappingTypes::MAPPING_TYPE_KEYWORD) {
                        $source[] = sprintf('%s.%s', $fieldId, $propertyKey);
                        break;
                    }
                }
            }
        }

        if ($source) {
            $query
                ->setSource($source)/** @phpstan-ignore-line */
                ->setStoredFields([]);
        }

        return $query;
    }

    /**
     * @param BoolQuery $boolQuery
     * @param User $user
     * @param LibraryFormDto|null $formDto
     *
     * @return BoolQuery
     */
    public function applySearchMode(BoolQuery $boolQuery, User $user, ?LibraryFormDto $formDto = null): BoolQuery
    {
        if ($formDto?->getQuery()) {
            return $this->addMultiWordSearchConditions($boolQuery, $user, $formDto->getQuery());
        }

        return $this->matchAllResults($boolQuery);
    }

    /**
     * @param BoolQuery $boolQuery
     * @param User $user
     * @param string $searchString
     *
     * @return BoolQuery
     */
    private function addMultiWordSearchConditions(BoolQuery $boolQuery, User $user, string $searchString): BoolQuery
    {
        $words = (array)preg_split('/\s+/', $searchString);

        foreach ($words as $word) {
            $value = sprintf('*%s*', $word);

            $wordBoolQuery = new BoolQuery();

            foreach ($this->mappingManager->getFiltersMapping($user) as $fieldId => $data) {
                $fieldType = $data['type'];

                if ($fieldType === MappingTypes::MAPPING_TYPE_KEYWORD) {
                    $wildCardQuery = new Wildcard((string)$fieldId, $value);
                    $wordBoolQuery->addShould($wildCardQuery);
                } elseif ($fieldType === MappingTypes::MAPPING_TYPE_TEXT) {
                    $queryStringQuery = new QueryString($value);
                    $queryStringQuery->setDefaultField((string) $fieldId);
                    $wordBoolQuery->addShould($queryStringQuery);
                } elseif ($fieldType === MappingTypes::MAPPING_TYPE_NESTED) {
                    $fieldProperties = $data['properties'];
                    foreach ($fieldProperties as $propertyKey => $propertyData) {
                        if ($propertyData['type'] === MappingTypes::MAPPING_TYPE_KEYWORD) {
                            $field = sprintf('%s.%s', $fieldId, $propertyKey);
                            $nestedQuery = new NestedQuery();
                            $nestedQuery->setPath((string) $fieldId);
                            $nestedWildcardQuery = new Wildcard($field, $value);
                            $nestedQuery->setQuery($nestedWildcardQuery);
                            $wordBoolQuery->addShould($nestedQuery);
                        }
                    }
                }
            }

            $wordBoolQuery->setMinimumShouldMatch(1);
            $boolQuery->addMust($wordBoolQuery);
        }

        return $boolQuery;
    }

    /**
     * @param BoolQuery $boolQuery
     *
     * @return BoolQuery
     */
    private function matchAllResults(BoolQuery $boolQuery): BoolQuery
    {
        return $boolQuery->addMust(new Query\MatchAll());
    }
}
