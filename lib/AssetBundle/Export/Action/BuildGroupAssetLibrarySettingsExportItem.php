<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Action;

use Froq\AssetBundle\Export\DataTransferObject\BlockElementDto;
use Froq\AssetBundle\Export\DataTransferObject\GroupAssetLibrarySettingsExportItem;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\SettingsMetadata;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;

class BuildGroupAssetLibrarySettingsExportItem
{
    public function __invoke(GroupAssetLibrarySettings $groupAssetLibrarySettings): GroupAssetLibrarySettingsExportItem
    {
        return new GroupAssetLibrarySettingsExportItem(
            id: $groupAssetLibrarySettings->getId(),
            key: $groupAssetLibrarySettings->getKey(),
            path: $groupAssetLibrarySettings->getPath(),
            groupName: $groupAssetLibrarySettings->getGroupName(),

            assetLibraryFilterOptions: $this->createBlockElementDtoCollection($groupAssetLibrarySettings->getAssetLibraryFilterOptions()),
            assetLibraryColumnsOptions: $this->createBlockElementDtoCollection($groupAssetLibrarySettings->getAssetLibraryColumnsOptions()),
            assetLibrarySortOptions: $this->createBlockElementDtoCollection($groupAssetLibrarySettings->getAssetLibrarySortOptions()),

            assetInfoSectionTitle: $groupAssetLibrarySettings->getAssetInfoSectionTitle(),
            isAssetInfoSectionEnabled: $groupAssetLibrarySettings->getIsAssetInfoSectionEnabled(),
            assetInfoSectionItems: (array) $groupAssetLibrarySettings->getAssetInfoSectionItems()?->getData(),
            assetInfoSectionMetadata: $this->createSettingsMetadata($groupAssetLibrarySettings->getAssetInfoSectionMetadata()),

            skuSectionTitle: $groupAssetLibrarySettings->getSkuSectionTitle(),
            isSKUSectionEnabled: $groupAssetLibrarySettings->getIsSKUSectionEnabled(),
            skuSectionItems: (array) $groupAssetLibrarySettings->getSkuSectionItems()?->getData(),
            skuInfoSectionAttributes: $this->createSettingsMetadata($groupAssetLibrarySettings->getSkuInfoSectionAttributes()),

            projectSectionTitle: $groupAssetLibrarySettings->getProjectSectionTitle(),
            isProjectSectionEnabled: $groupAssetLibrarySettings->getIsProjectSectionEnabled(),
            projectSectionItems: (array) $groupAssetLibrarySettings->getProjectSectionItems()?->getData(),

            supplierSectionTitle: $groupAssetLibrarySettings->getSupplierSectionTitle(),
            isSupplierSectionEnabled: $groupAssetLibrarySettings->getIsSupplierSectionEnabled(),
            supplierSectionItems: (array) $groupAssetLibrarySettings->getSupplierSectionItems()?->getData(),

            printSectionTitle: $groupAssetLibrarySettings->getPrintSectionTitle(),
            isPrintSectionEnabled: $groupAssetLibrarySettings->getIsPrintSectionEnabled(),
            printSectionItems: (array) $groupAssetLibrarySettings->getPrintSectionItems()?->getData(),
        );
    }

    /**
     * @param array<int, array<string, BlockElement>>|null $options
     *
     * @return array<int<0, max>, array<string, array<int, BlockElementDto>>>
     */
    private function createBlockElementDtoCollection(?array $options): array
    {
        $blockElementDtoCollection = [];

        foreach ($options ?? [] as $blockElementItems) {
            $blockItemsFromData = [];
            foreach ($blockElementItems as $typeName => $blockElementItem) {
                if (!($blockElementItem instanceof BlockElement)) {
                    continue;
                }

                $blockItemsFromData[(string) $typeName][] = new BlockElementDto(
                    name: $blockElementItem->getName(),
                    type: $blockElementItem->getType(),
                    data: $blockElementItem->getData(),
                );
            }

            $blockElementDtoCollection[] = $blockItemsFromData;
        }

        return $blockElementDtoCollection;
    }

    /** @return array<string, string> */
    private function createSettingsMetadata(?Fieldcollection $fieldcollection): array
    {
        $metadata = [];

        foreach ($fieldcollection?->getItems() ?? [] as $item) {
            if (!($item instanceof SettingsMetadata)) {
                continue;
            }

            $metadata[(string) $item->getMetadataKey()] = (string) $item->getLabel();
        }

        return $metadata;
    }
}
