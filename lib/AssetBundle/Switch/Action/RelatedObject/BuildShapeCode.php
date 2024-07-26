<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\RelatedObject;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;

final class BuildShapeCode
{
    /**
     * @throws \Exception
     */
    public function __invoke(Fieldcollection $assetResourceMetadataFieldCollection, AssetResource $parentAssetResource): void
    {
        $items = $assetResourceMetadataFieldCollection->getItems();

        foreach ($items as $item) {
            if (!($item instanceof AssetResourceMetadata)) {
                continue;
            }

            if ($item->getMetadataKey() !== 'shapecode') {
                continue;
            }

            $value = $item->getMetadataValue();

            if (!str_starts_with((string) $value, 'SC')) {
                continue;
            }

            $modelLibraryAssetResources = (new AssetResource\Listing())
                ->addConditionParam('o_key LIKE ?', ["%$value%"])
                ->addConditionParam('o_path = ?', '/Customers/FroQ/3D_Model_Library/')
                ->addConditionParam('o_published = ?', true)
                ->load();

            foreach ($modelLibraryAssetResources as $modelLibraryAssetResource) {
                if (!($modelLibraryAssetResource instanceof AssetResource)) {
                    continue;
                }

                $parentAssetResource->setContains(array_values(array_filter(array_unique([...$parentAssetResource->getContains(), $modelLibraryAssetResource]))));
                $parentAssetResource->save();
            }
        }
    }
}
