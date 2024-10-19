<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\AssetResource;

use Carbon\Carbon;
use Froq\AssetBundle\Action\GetFileDateFromEmbeddedMetadata;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

class AssetResourceFileDateManager
{
    public function __construct(private readonly ApplicationLogger $applicationLogger, private readonly GetFileDateFromEmbeddedMetadata $getFileDateFromEmbeddedMetadata)
    {
    }

    /**
     * @param AssetResource $assetResource
     *
     * @return void
     *
     * @throws \Exception
     */
    public function updateFileDates(AssetResource $assetResource): void
    {
        $asset = $assetResource->getAsset();
        if (!($asset instanceof Asset)) {
            return;
        }

        try {
            $fileDate = ($this->getFileDateFromEmbeddedMetadata)($asset);

            if ($fileDate === null) {
                return;
            }

            $isUpdated = false;
            if (empty($assetResource->getFileCreateDate())) {
                $assetResource->setFileCreateDate(new Carbon(time: $fileDate->createDate));
                $isUpdated = true;
            }

            if (empty($assetResource->getFileModifyDate())) {
                $assetResource->setFileModifyDate(new Carbon(time: $fileDate->modifyDate));
                $isUpdated = true;
            }

            if ($isUpdated) {
                $assetResource->save();
            }
        } catch (\Exception $e) {
            $this->applicationLogger->error(message: $e->getMessage());

            throw new \Exception($e->getMessage());
        }

        $this->applicationLogger->info(message: sprintf('Added File Date in AssetResource: %s', $assetResource->getId()));
    }
}
