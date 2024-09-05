<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Froq\PortalBundle\Utility\IsLuceneQuery;
use Pimcore\Model\DataObject\User;

class AssetLibSearchQueryManager
{
    public function __construct(private readonly AssetLibMappingManager $mappingManager, private readonly IsLuceneQuery $isLuceneQuery)
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
                ->setSource($source)                /** @phpstan-ignore-line */
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
    public function applySearch(BoolQuery $boolQuery, User $user, ?LibraryFormDto $formDto = null): BoolQuery
    {
        $searchTerm = (string) $formDto?->getQuery();

        if (!$searchTerm) {
            return $boolQuery;
        }

        foreach ($this->mappingManager->getFiltersMapping($user) as $data) {
            $fieldType = $data['type'] ?? '';

            $wordBoolQuery = new BoolQuery();

            if ($fieldType === MappingTypes::MAPPING_TYPE_KEYWORD || $fieldType === MappingTypes::MAPPING_TYPE_TEXT) {
                if (($this->isLuceneQuery)($searchTerm)) {
                    $queryString = new QueryString();

                    $queryString->setDefaultOperator('AND');
                    $queryString->setQuery($searchTerm);
                    $wordBoolQuery->addShould($queryString);
                }

                if (!($this->isLuceneQuery)($searchTerm)) {
                    $queryString = new QueryString("\"$searchTerm\"");

                    $wordBoolQuery->addShould($queryString);
                }

                $wordBoolQuery->setMinimumShouldMatch(1);
                $boolQuery->addMust($wordBoolQuery);
            }
        }

        return $boolQuery;
    }
}
