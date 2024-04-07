<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection;

use Froq\PortalBundle\Api\Enum\AssetInfoSectionItems;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionCreationDate;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class BuildAssetInfoSectionCreationDate
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly PortalDetailExtension $portalDetailExtension,
    ) {
    }

    public function __invoke(AssetResource $assetResource, ?AssetInfoSectionItem $assetInfoSectionItem, User $user, GroupAssetLibrarySettings $userSetting): AssetInfoSectionCreationDate
    {
        $name = AssetInfoSectionItems::CreationDate->readable();

        $isCreationDateEnabled = (bool) $assetInfoSectionItem?->isEnabled;

        $tableRowLabelCreationDate = $isCreationDateEnabled ? AssetDetailSettingsManager::getAvailableStructuredTableRowLabel(
            settings: $userSetting,
            structuredTableName: StructuredTableNames::AssetInfoSectionItems->readable(),
            rowKey: $name
        ) : '-';

        $isFilterAvailableForUser = $assetResource->getAssetType() &&
            $assetResource->getAssetType()->getName() &&
            $this->assetLibraryExtension->isFilterAvailableForUser($user, $name);

        $fileDateAdded = date('Y-m-d', (int) $this->portalDetailExtension->portalAssetResourceFileDateAdded($assetResource));

        return new AssetInfoSectionCreationDate(
            name: $name,
            isEnabled: $isCreationDateEnabled,
            label: (string) $assetInfoSectionItem?->label,
            tableRowLabelAssetTypeName: (string) $tableRowLabelCreationDate,
            isFilterAvailableForUser: $isFilterAvailableForUser,
            fileDateAdded: $fileDateAdded
        );
    }
}
