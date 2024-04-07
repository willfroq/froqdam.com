<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail;

use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetResourceDetail;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\SettingsItem;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class BuildAssetResourceDetail
{
    public function __construct(
        private readonly BuildProductCollection $buildProductCollection,
        private readonly BuildProjectCollection $buildProjectCollection,
        private readonly BuildAssetItem $buildAssetItem,
        private readonly AssetLibraryExtension $assetLibraryExtension,
    ) {
    }

    public function __invoke(AssetResource $assetResource, GroupAssetLibrarySettings $userSettings, SettingsItem $settingsItem, User $user): AssetResourceDetail
    {
        $products = null;
        $projects = null;

        if ($userSettings->getIsSKUSectionEnabled()) {
            $products = ($this->buildProductCollection)($assetResource, $userSettings, $user);
        }

        if ($userSettings->getIsProjectSectionEnabled()) {
            $projects = ($this->buildProjectCollection)($assetResource, $userSettings, $user);
        }

        return new AssetResourceDetail(
            id: (int) $assetResource->getId(),
            portalAssetDownloadPath: $this->assetLibraryExtension->portalAssetPath($assetResource->getAsset()),
            assetItem: ($this->buildAssetItem)($assetResource),
            products: $products,
            projects: $projects,
            settings: $settingsItem
        );
    }
}
