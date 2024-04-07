<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig;

use Froq\AssetBundle\Manager\AssetResource\AssetResourceFieldCollectionsManager;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibMappingManager;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\ColumnConfigurationManager;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\FilterConfigurationManager;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\SortConfigurationManager;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetLibraryExtension extends AbstractExtension
{
    public function __construct(protected AssetLibMappingManager $libMappingManager,
        protected ColumnConfigurationManager $columnConfigManager,
        protected SortConfigurationManager $sortConfigManager,
        protected FilterConfigurationManager $filterConfigManager,
        protected AssetLibMappingManager $assetLibMappingManager,
        protected ApplicationLogger $logger)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('portal_asset_path', [$this, 'portalAssetPath']),
            new TwigFunction('get_asset_thumbnail_path', [$this, 'getAssetThumbnailPath']),
            new TwigFunction('get_class_name', [$this, 'getClassName']),
            new TwigFunction('get_asset_library_columns_for_user', [$this, 'getAssetLibraryColumnsForUser']),
            new TwigFunction('get_available_column_label', [$this, 'getAvailableColumnLabel']),
            new TwigFunction('get_latest_asset_resource_version', [AssetResourceHierarchyHelper::class, 'getLatestVersion']),
            new TwigFunction('get_configured_columns_keys_for_user', [ColumnConfigurationManager::class, 'getColumnKeysForUser']),
            new TwigFunction('get_configured_sort_keys_for_user', [SortConfigurationManager::class, 'getSortKeysForUser']),
            new TwigFunction('get_asset_resource_metadata_value_by_key', [AssetResourceFieldCollectionsManager::class, 'getMetadataValueByKey']),
            new TwigFunction('get_sku_attribute_value_by_key', [AssetResourceFieldCollectionsManager::class, 'getSkuAttributeValueByKey']),
            new TwigFunction('is_asset_library_keyword_filter_available_for_user', [$this, 'isFilterAvailableForUser']),
        ];
    }

    public function getName(): string
    {
        return 'froq_portal_asset_twig_extension';
    }

    public function portalAssetPath(Asset|null $asset): string
    {
        $assetPath = $asset?->getFrontendPath();

        if (empty($assetPath)) {
            return '';
        }

        return $assetPath;
    }

    public function getAssetThumbnailPath(Asset|null $asset, string $thumbnailName): string|null
    {
        try {
            if ($asset instanceof Asset\Image) {
                return $asset->getThumbnail($thumbnailName, false)->getFrontendPath();
            } elseif ($asset instanceof Asset\Document) {
                return $asset->getImageThumbnail($thumbnailName)->getFrontendPath();
            }
        } catch (\Exception $ex) {
            $this->logger->critical($ex->getMessage());
        }

        return null;
    }

    public function getClassName(object $object): ?string
    {
        return get_class($object);
    }

    /**
     * @return array<array<BlockElement>>|null
     */
    public function getAssetLibraryColumnsForUser(User $user): ?array
    {
        return $this->columnConfigManager->getAssetLibraryColumnsOptions($user);
    }

    public function getAvailableColumnLabel(string $columnId, User $user): ?string
    {
        return $this->columnConfigManager->getAvailableColumnLabel($columnId, $user);
    }

    public function isFilterAvailableForUser(User $user, string $filterID): bool
    {
        return $this->assetLibMappingManager->isKeywordFilterAvailableForUser($user, $filterID);
    }
}
