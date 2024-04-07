<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection;

use Froq\PortalBundle\Api\Enum\AssetInfoSectionItems;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionAssetVersion;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class BuildAssetInfoSectionAssetVersion
{
    public function __construct(private readonly PortalDetailExtension $portalDetailExtension)
    {
    }

    public function __invoke(AssetResource $assetResource, ?AssetInfoSectionItem $assetInfoSectionItem, User $user, GroupAssetLibrarySettings $userSetting): AssetInfoSectionAssetVersion
    {
        $name = AssetInfoSectionItems::AssetVersion->readable();

        $isAssetTypeNameEnabled = $assetInfoSectionItem?->isEnabled;

        return new AssetInfoSectionAssetVersion(
            name: $name,
            isEnabled: (bool) $isAssetTypeNameEnabled,
            label: (string) $assetInfoSectionItem?->label,
            tableRowLabelAssetTypeName: (string) $isAssetTypeNameEnabled ? (string) AssetDetailSettingsManager::getAvailableStructuredTableRowLabel(
                settings: $userSetting,
                structuredTableName: StructuredTableNames::AssetInfoSectionItems->readable(),
                rowKey: $name
            ) : '-',
            assetResourceVersion: $this->portalDetailExtension->portalAssetResourceVersion($assetResource)
        );
    }
}
