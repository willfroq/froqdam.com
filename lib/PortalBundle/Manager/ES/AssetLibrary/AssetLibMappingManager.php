<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\ColumnConfigurationManager;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\FilterConfigurationManager;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\SortConfigurationManager;
use Pimcore\Model\DataObject\User;
use Youwe\PimcoreElasticsearchBundle\Service\IndexListingServiceInterface;

class AssetLibMappingManager
{
    public const MAPPING_ORGANIZATION_ID = 'organization_id';
    public const MAPPING_PDF_TEXT = 'pdf_text';

    /** @var array<int|string, mixed> */
    protected static array $userListMappings = [];

    public function __construct(
        private readonly FilterConfigurationManager $filterConfigManager,
        private readonly ColumnConfigurationManager $columnConfigManager,
        private readonly SortConfigurationManager $sortConfigManager,
        private readonly IndexListingServiceInterface $esIndexListingManager,
        private readonly string $esIndexIdAssetLib
    ) {
    }

    /**
     * @return array<string, mixed> | null
     */
    public function getAssetLibMappingDefinition(): ?array
    {
        $index = $this->esIndexListingManager->getIndex($this->esIndexIdAssetLib);

        return $index?->getMapping()->getDefinition();
    }

    /**
     * @param User $user
     *
     * @return array<int|string, mixed>
     */
    public function getFiltersMapping(User $user): array
    {
        $cachedResult = static::$userListMappings[$user->getId()] ?? null;

        if ($cachedResult) {
            return $cachedResult;
        }

        $configuredIds = $this->filterConfigManager->getConfiguredFilterIdsForUser($user);
        if (!$configuredIds) {
            return [];
        }

        $libMapping = $this->getAssetLibMappingDefinition();
        $result = [];

        foreach ($configuredIds as $filterId) {
            if (isset($libMapping[$filterId])) {
                $result[$filterId] = $libMapping[$filterId];
            }
        }

        return static::$userListMappings[$user->getId()] = $result;
    }

    /**
     * @param User $user
     *
     * @return array<int|string, mixed>
     */
    public function getColumnsMapping(User $user): array
    {
        $userColumnKeys = $this->columnConfigManager->getColumnKeysForUser($user);
        if (!$userColumnKeys) {
            return [];
        }

        $libMapping = $this->getAssetLibMappingDefinition();
        $result = [];

        foreach ($userColumnKeys as $columnId) {
            if (isset($libMapping[$columnId])) {
                $result[$columnId] = $libMapping[$columnId];
            }
        }

        return $result;
    }

    /**
     * @param User $user
     *
     * @return array<string|int, mixed>
     */
    public function getSortMapping(User $user): array
    {
        $result = [];
        $libMapping = $this->getAssetLibMappingDefinition();

        foreach ($this->sortConfigManager->getSortKeysForUser($user) as $columnId) {
            if (isset($libMapping[$columnId])) {
                $result[$columnId] = $libMapping[$columnId];
            }
        }

        return $result;
    }

    public function isKeywordFilterAvailableForUser(User $user, string $filterID): bool
    {
        $mapping = $this->getFiltersMapping($user);

        if (!isset($mapping[$filterID])) {
            return false;
        }

        if ($mapping[$filterID] === 'pdf_text') {
            return false;
        }

        if ($mapping[$filterID]['type'] === MappingTypes::MAPPING_TYPE_TEXT) {
            return true;
        }

        if ($mapping[$filterID]['type'] === MappingTypes::MAPPING_TYPE_KEYWORD) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    public function getFieldPath()
    {
        // Todo Implement the functionality to standardize the nested field path in the query
    }
}
