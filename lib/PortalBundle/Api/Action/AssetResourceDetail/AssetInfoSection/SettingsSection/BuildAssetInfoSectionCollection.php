<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection;

use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\AssetInfoSectionItem;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;

final class BuildAssetInfoSectionCollection
{
    public function __invoke(GroupAssetLibrarySettings $userSettings): AssetInfoSectionCollection
    {
        $assetInfoCollection = new AssetInfoSectionCollection();

        foreach ($userSettings->getAssetInfoSectionItems()?->getData() ?? [] as $key => $value) {
            $assetInfoCollection->add(new AssetInfoSectionItem(
                name: $key, isEnabled: (bool) $value['enabled'], label: (string) $value['label']
            ));
        }

        return $assetInfoCollection;
    }
}
