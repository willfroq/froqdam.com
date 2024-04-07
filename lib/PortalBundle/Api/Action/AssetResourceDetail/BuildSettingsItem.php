<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail;

use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\BuildAssetInfoSectionAssetCreationDate;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\BuildAssetInfoSectionAssetTypeName;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\BuildAssetInfoSectionAssetVersion;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\BuildAssetInfoSectionCreationDate;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\BuildAssetInfoSectionLastModified;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\BuildAssetInfoSectionMetadataCollection;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection\BuildAssetInfoSectionCollection;
use Froq\PortalBundle\Api\Enum\AssetInfoSectionItems;
use Froq\PortalBundle\Api\Enum\SectionFieldTitles;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\SettingsItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class BuildSettingsItem
{
    public function __construct(
        private readonly AssetDetailSettingsManager $assetDetailSettingsManager,
        private readonly BuildAssetInfoSectionAssetTypeName $buildAssetInfoSectionAssetTypeName,
        private readonly BuildAssetInfoSectionCreationDate $buildAssetInfoSectionCreationDate,
        private readonly BuildAssetInfoSectionAssetCreationDate $buildAssetInfoSectionAssetCreationDate,
        private readonly BuildAssetInfoSectionLastModified $buildAssetInfoSectionLastModified,
        private readonly BuildAssetInfoSectionAssetVersion $buildAssetInfoSectionAssetVersion,
        private readonly BuildAssetInfoSectionMetadataCollection $buildAssetInfoSectionMetadata,
        private readonly BuildAssetInfoSectionCollection $buildAssetInfoSectionCollection,
    ) {
    }

    public function __invoke(AssetResource $assetResource, User $user, GroupAssetLibrarySettings $userSettings): SettingsItem
    {
        $isAssetInfoEnabled = (bool) $userSettings->getIsAssetInfoSectionEnabled();
        $assetDetailConfigLabel = '';

        $assetInfoSectionAssetTypeName = null;
        $assetInfoSectionCreationDate = null;
        $assetInfoSectionAssetCreationDate = null;
        $assetInfoSectionLastModified = null;
        $assetInfoSectionAssetVersion = null;
        $assetInfoSectionMetadata = null;

        if ($isAssetInfoEnabled) {
            $assetDetailConfigLabel = (string) $this->assetDetailSettingsManager->getAvailableSectionLabel(
                $userSettings, SectionFieldTitles::AssetInfoSectionTitle->readable()
            );

            $assetInfoCollection = ($this->buildAssetInfoSectionCollection)($userSettings);

            $assetInfoSectionAssetTypeName = ($this->buildAssetInfoSectionAssetTypeName)(
                $assetResource,
                $assetInfoCollection->getItemByName(AssetInfoSectionItems::AssetTypeName->readable()),
                $user,
                $userSettings
            );

            $assetInfoSectionCreationDate = ($this->buildAssetInfoSectionCreationDate)(
                $assetResource,
                $assetInfoCollection->getItemByName(AssetInfoSectionItems::CreationDate->readable()),
                $user,
                $userSettings
            );

            $assetInfoSectionAssetCreationDate = ($this->buildAssetInfoSectionAssetCreationDate)(
                $assetResource,
                $assetInfoCollection->getItemByName(AssetInfoSectionItems::AssetCreationDate->readable()),
                $user,
                $userSettings
            );

            $assetInfoSectionLastModified = ($this->buildAssetInfoSectionLastModified)(
                $assetResource,
                $assetInfoCollection->getItemByName(AssetInfoSectionItems::LastModified->readable()),
                $user,
                $userSettings
            );

            $assetInfoSectionAssetVersion = ($this->buildAssetInfoSectionAssetVersion)(
                $assetResource,
                $assetInfoCollection->getItemByName(AssetInfoSectionItems::AssetVersion->readable()),
                $user,
                $userSettings
            );

            $assetInfoSectionMetadata = ($this->buildAssetInfoSectionMetadata)(
                $assetResource,
                $user,
                $userSettings
            );
        }

        return new SettingsItem(
            id: (int) $userSettings->getId(),
            assetDetailConfigLabel: $assetDetailConfigLabel,
            assetInfoSectionAssetTypeName: $assetInfoSectionAssetTypeName,
            assetInfoSectionCreationDate: $assetInfoSectionCreationDate,
            assetInfoSectionAssetCreationDate: $assetInfoSectionAssetCreationDate,
            assetInfoSectionLastModified: $assetInfoSectionLastModified,
            assetInfoSectionAssetVersion: $assetInfoSectionAssetVersion,
            assetInfoSectionMetadata: $assetInfoSectionMetadata
        );
    }
}
