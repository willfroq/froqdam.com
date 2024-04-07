<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection;

use Froq\PortalBundle\Api\Enum\AssetInfoSectionItems;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionLastModified;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class BuildAssetInfoSectionLastModified
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly PortalDetailExtension $portalDetailExtension,
    ) {
    }

    public function __invoke(AssetResource $assetResource, ?AssetInfoSectionItem $assetInfoSectionItem, User $user, GroupAssetLibrarySettings $userSetting): AssetInfoSectionLastModified
    {
        $name = AssetInfoSectionItems::LastModified->readable();

        $isFileLastModifiedEnabled = (bool) $assetInfoSectionItem?->isEnabled;

        $tableRowLabelFileLastModified = $isFileLastModifiedEnabled ? AssetDetailSettingsManager::getAvailableStructuredTableRowLabel(
            settings: $userSetting,
            structuredTableName: StructuredTableNames::AssetInfoSectionItems->readable(),
            rowKey: $name
        ) : '-';

        $isFilterAvailableForUser = $assetResource->getAssetType() &&
            $assetResource->getAssetType()->getName() &&
            $this->assetLibraryExtension->isFilterAvailableForUser($user, $name);

        $fileLastModified = date('Y-m-d', (int) $this->portalDetailExtension->portalAssetResourceFileDateModified($assetResource));

        return new AssetInfoSectionLastModified(
            name: $name,
            isEnabled: $isFileLastModifiedEnabled,
            label: (string) $assetInfoSectionItem?->label,
            tableRowLabelAssetTypeName: (string) $tableRowLabelFileLastModified,
            isFilterAvailableForUser: $isFilterAvailableForUser,
            fileLastModified: $fileLastModified
        );
    }
}
