<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\AssetResource;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Product;

class AssetResourceFieldCollectionsManager
{
    public function updateMetadata(AssetResource $assetResource): void
    {
        $asset = $assetResource->getAsset();
        if (!$asset) {
            return;
        }

        if (!method_exists($asset, 'getEmbeddedMetaData')) {
            return;
        }

        if (!$asset->getEmbeddedMetaData(false)) {
            $asset->getEmbeddedMetaData(true, false); // read Exif, IPTC and XPM like in the old days ...
        }

        $embeddedMetaData = $asset->getCustomSettings()['embeddedMetaData'] ?? [];
        if (!$embeddedMetaData) {
            return;
        }

        $fieldCollectionItems = $this->initializeItems($assetResource);

        foreach ($embeddedMetaData as $key => $value) {
            $available = false;
            /** @var AssetResourceMetadata $item */
            $arMetadata = $assetResource->getMetadata();
            if ($arMetadata) {
                foreach ($arMetadata->getItems() as $item) {
                    if (!($item instanceof AssetResourceMetadata)) {
                        continue;
                    }

                    if ($item->getMetadataKey() === $key) {
                        $item->setMetadataValue($value);
                        $fieldCollectionItems[$key] = $item;
                        $available = true;
                        break;
                    }
                }
            }

            if (!$available) {
                $fieldCollectionItems[$key] = $this->createAssetResourceMetadata($assetResource, $key, $value);
            }
        }

        $fc = new Fieldcollection($fieldCollectionItems, 'Metadata');
        $fc->setObject($assetResource);

        $assetResource->setMetadata($fc);
        $assetResource->save();
    }

    public static function getMetadataValueByKey(AssetResource $assetResource, string $key): ?string
    {
        if ($arMetadata = $assetResource->getMetadata()) {
            foreach ($arMetadata->getItems() as $item) {
                if (!($item instanceof AssetResourceMetadata)) {
                    continue;
                }

                if ($item->getMetadataKey() === $key) {
                    return $item->getMetadataValue();
                }
            }
        }

        return null;
    }

    public static function getSkuAttributeValueByKey(Product $product, string $key): ?string
    {
        if ($productAttributes = $product->getAttributes()) {
            foreach ($productAttributes->getItems() as $item) {
                if (!($item instanceof ProductAttributes)) {
                    continue;
                }

                if ($item->getAttributeKey() === $key) {
                    return $item->getAttributeValue();
                }
            }
        }

        return null;
    }

    private function createAssetResourceMetadata(AssetResource $assetResource, string $key, string $value): AssetResourceMetadata
    {
        $metadata = new AssetResourceMetadata();
        $metadata->setMetadataKey($key);
        $metadata->setMetadataValue($value);
        $metadata->setObject($assetResource);

        return $metadata;
    }

    /**
     * @return array<string|int, mixed>
     */
    private function initializeItems(AssetResource $assetResource): array
    {
        $items = [];
        $arMetadata = $assetResource->getMetadata();
        if ($arMetadata) {
            /** @var AssetResourceMetadata $item */
            foreach ($arMetadata->getItems() as $item) {
                $items[$item->getMetadataKey()] = $item;
            }
        }

        return $items;
    }
}
