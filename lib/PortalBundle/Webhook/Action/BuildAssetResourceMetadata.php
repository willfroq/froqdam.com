<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Action;

use Exception;
use Froq\AssetBundle\Action\BuildAllMetadata;
use Froq\AssetBundle\Action\BuildMetadataFromFilename;
use Froq\AssetBundle\Action\BuildXmpMetadata;
use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;

final class BuildAssetResourceMetadata
{
    public function __construct(
        private readonly BuildXmpMetadata $buildXmpMetadata,
        private readonly BuildMetadataFromFilename $buildMetadataFromFilename,
        private readonly BuildAllMetadata $buildAllMetadata,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, Asset $asset): ?DataObject\Fieldcollection
    {
        $assetResourceMetadataFieldCollection = match ($switchUploadRequest->metadataFrom) {
            'XMP only' => $asset instanceof AssetDocument ? ($this->buildXmpMetadata)($asset) : throw new Exception("'XMP only' has to be a pdf file"),
            'Filename only' => ($this->buildMetadataFromFilename)($asset, $switchUploadRequest),

            default => ($this->buildAllMetadata)($asset, $switchUploadRequest)
        };

        if (!($assetResourceMetadataFieldCollection instanceof DataObject\Fieldcollection)) {
            return null;
        }

        return $assetResourceMetadataFieldCollection;
    }
}
