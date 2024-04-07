<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Froq\AssetBundle\ValueObject\XmpMetadata;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;

final class BuildAllMetadata
{
    /**
     * @throws \Exception
     */
    public function __invoke(Asset|AssetDocument|Asset\Image $asset, SwitchUploadRequest $switchUploadRequest): Fieldcollection
    {
        $xmpMetadata = new XmpMetadata();

        if ($asset instanceof AssetDocument || $asset instanceof Asset\Image) {
            $embeddedMetadata = $asset->getEmbeddedMetaData(true);

            $xmpMetadataKeys = array_keys(get_object_vars($xmpMetadata));

            foreach ($embeddedMetadata ?? [] as $key => $value) {
                if (!in_array(needle: $key, haystack: $xmpMetadataKeys)) {
                    continue;
                }

                if (empty($value)) {
                    continue;
                }

                $xmpMetadata->{$key} = $value;
            }

            $exifData = $asset->getEXIFData($switchUploadRequest->fileContents?->getPath());

            foreach ($exifData ?? [] as $key => $value) {
                if (!in_array(needle: $key, haystack: $xmpMetadataKeys)) {
                    continue;
                }

                if (empty($value)) {
                    continue;
                }

                $xmpMetadata->{$key} = $value;
            }
        }

        $separator = empty($switchUploadRequest->filenameSeparator) ? '_' : $switchUploadRequest->filenameSeparator;

        $fileNameArray = explode($separator, (string)$asset->getFilename());

        $metadataMapping = (array)json_decode((string)$switchUploadRequest->metadataMapping);

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

        foreach ($fileNameArray as $index => $filenameSegment) {
            if (!in_array(needle: $filenameSegment, haystack: $metadataMapping)) {
                continue;
            }

            foreach ($metadataMapping as $key => $item) {
                if ((int)$key !== $index + 1) {
                    continue;
                }

                if (empty($filenameSegment)) {
                    continue;
                }

                $assetResourceMetadata = new AssetResourceMetadata();
                $assetResourceMetadata->setMetadataKey($item);
                $assetResourceMetadata->setMetadataValue($filenameSegment);

                $fieldCollectionItems[] = $assetResourceMetadata;
            }
        }

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($fieldCollectionItems);

        return $fieldCollection;
    }
}
