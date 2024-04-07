<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection;

use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\Enum\AssetInfoSectionItems;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionAssetTypeName;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildAssetInfoSectionAssetTypeName
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly GetBaseUrl $getBaseUrl,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(AssetResource $assetResource, ?AssetInfoSectionItem $assetInfoSectionItem, User $user, GroupAssetLibrarySettings $userSetting): AssetInfoSectionAssetTypeName
    {
        $name = AssetInfoSectionItems::AssetTypeName->readable();

        $isAssetTypeNameEnabled = $assetInfoSectionItem?->isEnabled;

        $isFilterAvailableForUser = $assetResource->getAssetType() &&
            $assetResource->getAssetType()->getName() &&
            $this->assetLibraryExtension->isFilterAvailableForUser($user, $name);

        return new AssetInfoSectionAssetTypeName(
            name: $name,
            isEnabled: (bool) $isAssetTypeNameEnabled,
            label: (string) $assetInfoSectionItem?->label,
            tableRowLabelAssetTypeName: (string) $isAssetTypeNameEnabled ? (string) AssetDetailSettingsManager::getAvailableStructuredTableRowLabel(
                settings: $userSetting,
                structuredTableName: StructuredTableNames::AssetInfoSectionItems->readable(),
                rowKey: $name
            ) : '',
            isFilterAvailableForUser: $isFilterAvailableForUser,
            assetTypeLink: (string) $isFilterAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                    'code' => $user->getCode(),
                    'filters' => [
                        'asset_type_name' => [strtolower((string) $assetResource->getAssetType()?->getName())]
                    ]
                ]) : '',
            assetTypeName: (string) $assetResource->getAssetType()?->getName()
        );
    }
}
