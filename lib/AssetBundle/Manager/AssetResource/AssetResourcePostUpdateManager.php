<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\AssetResource;

use Pimcore\Model\DataObject\AssetResource;

class AssetResourcePostUpdateManager
{
    public function __construct(private readonly SFTPAssetImportManager $assetResourceImportManager)
    {
    }

    /**
     * @param AssetResource $assetResource
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handlePostUpdated(AssetResource $assetResource): void
    {
        $this->assetResourceImportManager->importAssetByUploadName($assetResource);
    }
}
