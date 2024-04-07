<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection;

use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\ProductInfoSectionCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\ProductInfoSectionItem;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;

final class BuildProductInfoSectionCollection
{
    public function __invoke(GroupAssetLibrarySettings $userSettings): ProductInfoSectionCollection
    {
        $productInfoCollection = new ProductInfoSectionCollection();

        foreach ($userSettings->getSkuSectionItems()?->getData() ?? [] as $key => $value) {
            $productInfoCollection->add(new ProductInfoSectionItem(
                name: $key, isEnabled: (bool) $value['enabled'], label: (string) $value['label']
            ));
        }

        return $productInfoCollection;
    }
}
