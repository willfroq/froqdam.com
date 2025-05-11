<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Action;

use Froq\AssetBundle\Export\DataTransferObject\GroupAssetLibraryExportCollection;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;

final class BuildExportGroupAssetLibraryCollection
{
    public function __construct(private readonly BuildGroupAssetLibrarySettingsExportItem $buildGroupAssetLibrarySettingsExportItem)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(int $offset, int $limit): GroupAssetLibraryExportCollection
    {
        $groupAssetLibrarySettings = GroupAssetLibrarySettings::getList()
            ->setOffset($offset)
            ->setLimit($limit)
            ->setOrderKey('GroupName')
            ->setOrder('asc');

        $groupAssetLibrarySettingsExportItems = [];

        foreach ($groupAssetLibrarySettings as $groupAssetLibrarySetting) {
            if (!($groupAssetLibrarySetting instanceof GroupAssetLibrarySettings)) {
                continue;
            }

            $groupAssetLibrarySettingsExportItems[] = ($this->buildGroupAssetLibrarySettingsExportItem)($groupAssetLibrarySetting);
        }

        return new GroupAssetLibraryExportCollection(
            offset: $offset,
            limit: $limit,
            totalCount: $groupAssetLibrarySettings->count(),
            groupAssetLibrarySettingsExportItems: $groupAssetLibrarySettingsExportItems
        );
    }
}
