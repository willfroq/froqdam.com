<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\AssetResource;

use Froq\AssetBundle\Message\PutFileMetadataInAssetResourceMessage;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Messenger\MessageBusInterface;

class AssetResourcePostUpdateManager
{
    public function __construct(private readonly SFTPAssetImportManager $assetResourceImportManager,
        private readonly MessageBusInterface $messageBus,
        private readonly AssetResourceFileDateManager $fileDateManager)
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

        $this->fileDateManager->updateFileDates($assetResource);

        $parentId = $assetResource->getParentId();
        if ($parentId) {
            $this->messageBus->dispatch(new PutFileMetadataInAssetResourceMessage(parentIds: [$parentId]));
        }
    }
}
