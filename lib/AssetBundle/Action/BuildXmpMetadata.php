<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Froq\AssetBundle\ValueObject\XmpMetadata;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;

final class BuildXmpMetadata
{
    /**
     * @throws \Exception
     */
    public function __invoke(AssetDocument $asset): Fieldcollection
    {
        $assetMetadata = $asset->getEmbeddedMetaData(true);

        $xmpMetadata = new XmpMetadata();

        $xmpMetadataKeys = array_keys(get_object_vars($xmpMetadata));

        foreach ($assetMetadata as $key => $value) {
            if (!in_array(needle: $key, haystack: $xmpMetadataKeys)) {
                continue;
            }

            if (empty($value)) {
                continue;
            }

            $xmpMetadata->{$key} = $value;
        }

        $fieldCollectionItems = [];

        foreach ($xmpMetadata->toArray() as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $assetResourceMetadata = new AssetResourceMetadata();
            $assetResourceMetadata->setMetadataKey($key);
            $assetResourceMetadata->setMetadataValue($value);

            $fieldCollectionItems[] = $assetResourceMetadata;
        }

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($fieldCollectionItems);

        return $fieldCollection;
    }
}
