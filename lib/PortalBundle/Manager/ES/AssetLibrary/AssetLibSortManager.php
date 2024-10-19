<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\SortConfigurationManager;
use Pimcore\Model\DataObject\User;

class AssetLibSortManager
{
    public const DEFAULT_SORT_BY = 'creation_date';
    public const DEFAULT_SORT_DIRECTION = 'desc';

    public function __construct(private readonly AssetLibMappingManager $mappingManager,
        private readonly SortConfigurationManager $sortConfigManager)
    {
    }

    /**
     * @param Query $query
     * @param User $user
     * @param LibraryFormDto|null $libraryFormDto
     *
     * @return Query
     */
    public function sort(Query $query, User $user, LibraryFormDto $libraryFormDto = null): Query
    {
        $sortBy = $libraryFormDto?->getSortBy() ?? self::DEFAULT_SORT_BY;
        $sortDirection = $libraryFormDto?->getSortDirection() ?? self::DEFAULT_SORT_DIRECTION;

        if (!empty($this->getSortableItems($user)[$sortBy])) {
            $query->setSort([$sortBy => ['order' => $sortDirection]]);
        }

        return $query;
    }

    /**
     * @return array<string>
     */
    public function getSortableItems(User $user): array
    {
        $items = [self::DEFAULT_SORT_BY => self::DEFAULT_SORT_BY];

        $sortFieldsMapping = $this->mappingManager->getSortMapping($user);
        $columnFieldsMapping = $this->mappingManager->getColumnsMapping($user);

        $combinedFields = array_merge($sortFieldsMapping, $columnFieldsMapping);

        foreach ($this->getNonSortableFieldIds() as $fieldId) {
            if (!is_string($fieldId)) {
                continue;
            }

            unset($combinedFields[$fieldId]);
        }

        foreach ($combinedFields as $fieldId => $data) {
            $type = $data['type'];
            $label = $this->sortConfigManager->getAvailableSortLabel((string) $fieldId, $user);
            if (!$label) {
                continue;
            }

            if ($type === MappingTypes::MAPPING_TYPE_NESTED) {
                $fieldProperties = $data['properties'];
                foreach ($fieldProperties as $propertyKey => $propertyData) {
                    if ($propertyData['type'] === MappingTypes::MAPPING_TYPE_KEYWORD) {
                        $fieldPath = sprintf('%s.%s', $fieldId, $propertyKey);
                        $items[$fieldPath] = $label;
                        break;
                    }
                }
            } else {
                $items[$fieldId] = $label;
            }
        }

        return $items;
    }

    /**
     * @return array<string, array<int, string>> | array<int, string>
     */
    private function getNonSortableFieldIds(): array
    {
        return [AssetLibMappingManager::MAPPING_PDF_TEXT];
    }
}
