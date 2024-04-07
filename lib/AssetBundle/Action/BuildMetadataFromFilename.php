<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;

final class BuildMetadataFromFilename
{
    public function __invoke(Asset|Asset\Image $asset, SwitchUploadRequest $switchUploadRequest): Fieldcollection
    {
        $separator = empty($switchUploadRequest->filenameSeparator) ? '_' : $switchUploadRequest->filenameSeparator;

        $fileNameArray = explode($separator, (string) $asset->getFilename());

        $metadataMapping = (array) json_decode((string) $switchUploadRequest->metadataMapping);

        $fieldCollectionItems = [];

        foreach ($fileNameArray as $index => $filenameSegment) {
            if (!in_array(needle: $filenameSegment, haystack: $metadataMapping)) {
                continue;
            }

            foreach ($metadataMapping as $key => $item) {
                if ((int) $key !== $index + 1) {
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
