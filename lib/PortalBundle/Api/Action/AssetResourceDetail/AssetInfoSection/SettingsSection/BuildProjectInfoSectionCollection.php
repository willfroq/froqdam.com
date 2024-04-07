<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection;

use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\ProjectInfoSectionCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection\ProjectInfoSectionItem;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;

final class BuildProjectInfoSectionCollection
{
    public function __invoke(GroupAssetLibrarySettings $userSettings): ProjectInfoSectionCollection
    {
        $projectInfoCollection = new ProjectInfoSectionCollection();

        foreach ($userSettings->getProjectSectionItems()?->getData() ?? [] as $key => $value) {
            $projectInfoCollection->add(new ProjectInfoSectionItem(
                name: $key, isEnabled: (bool) $value['enabled'], label: (string) $value['label']
            ));
        }

        return $projectInfoCollection;
    }
}
