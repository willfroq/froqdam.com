<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;

final class BuildAssetResourceMetadata
{
    public function __construct(private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest): ?DataObject\Fieldcollection
    {
        $metadata = (array) json_decode($switchUploadRequest->assetResourceMetadataFieldCollection, true);

        if (empty($metadata) || ($this->allPropsEmptyOrNull)($metadata)) {
            return null;
        }

        $fieldCollectionItems = [];

        foreach ($metadata as $item) {
            if (count((array) $item) !== 1) {
                continue;
            }

            $assetResourceMetadata = new AssetResourceMetadata();

            $assetResourceMetadata->setMetadataKey((string) array_key_first($item));
            $assetResourceMetadata->setMetadataValue(current($item));

            $fieldCollectionItems[] = $assetResourceMetadata;
        }

        if (empty($fieldCollectionItems)) {
            return null;
        }

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($fieldCollectionItems);

        return $fieldCollection;
    }
}
