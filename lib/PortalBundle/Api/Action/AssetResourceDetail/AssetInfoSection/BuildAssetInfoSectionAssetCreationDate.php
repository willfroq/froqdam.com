<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection;

use Froq\PortalBundle\Api\Enum\AssetInfoSectionItems;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionAssetCreationDate;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class BuildAssetInfoSectionAssetCreationDate
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly PortalDetailExtension $portalDetailExtension,
    ) {
    }

    public function __invoke(AssetResource $assetResource, ?AssetInfoSectionItem $assetInfoSectionItem, User $user, GroupAssetLibrarySettings $userSetting): AssetInfoSectionAssetCreationDate
    {
        $name = AssetInfoSectionItems::AssetCreationDate->readable();

        $isAssetCreationDateEnabled = (bool) $assetInfoSectionItem?->isEnabled;

        $tableRowLabelCreationDate = $isAssetCreationDateEnabled ? AssetDetailSettingsManager::getAvailableStructuredTableRowLabel(
            settings: $userSetting,
            structuredTableName: StructuredTableNames::AssetInfoSectionItems->readable(),
            rowKey: $name
        ) : '-';

        $isFilterAvailableForUser = $assetResource->getAssetType() &&
            $assetResource->getAssetType()->getName() &&
            $this->assetLibraryExtension->isFilterAvailableForUser($user, $name);

        $fileDateCreated = date('Y-m-d', (int) $this->portalDetailExtension->portalAssetResourceFileDateCreated($assetResource));

        return new AssetInfoSectionAssetCreationDate(
            name: $name,
            isEnabled: $isAssetCreationDateEnabled,
            label: (string) $assetInfoSectionItem?->label,
            tableRowLabelAssetTypeName: (string) $tableRowLabelCreationDate,
            isFilterAvailableForUser: $isFilterAvailableForUser,
            fileDateCreated: $fileDateCreated
        );
    }
}
