<?php

declare(strict_types=1);

namespace Froq\AssetBundle\EventSubscriber;

use Carbon\Carbon;
use Froq\AssetBundle\Action\GetFileDateFromEmbeddedMetadata;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetResourceFileDateSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ApplicationLogger $applicationLogger, private readonly GetFileDateFromEmbeddedMetadata $getFileDateFromEmbeddedMetadata)
    {
    }

    /** @return string[] */
    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::POST_ADD => 'onPostAddOrUpdate',
            DataObjectEvents::POST_UPDATE => 'onPostAddOrUpdate',
        ];
    }

    /**
     * @throws \Exception
     */
    public function onPostAddOrUpdate(DataObjectEvent $event): void
    {
        $assetResource = $event->getObject();

        if (!($assetResource instanceof AssetResource)) {
            return;
        }

        $asset = $assetResource->getAsset();

        if (!($asset instanceof Asset)) {
            return;
        }

        try {
            $fileDate = ($this->getFileDateFromEmbeddedMetadata)($asset);

            if ($fileDate === null) {
                return;
            }

            if (empty($assetResource->getFileCreateDate())) {
                $assetResource->setFileCreateDate(new Carbon(time: $fileDate->createDate));

                $assetResource->save();
            }

            if (empty($assetResource->getFileModifyDate())) {
                $assetResource->setFileModifyDate(new Carbon(time: $fileDate->modifyDate));

                $assetResource->save();
            }
        } catch (\Exception $e) {
            $this->applicationLogger->error(message: $e->getMessage());

            throw new \Exception($e->getMessage());
        }

        $this->applicationLogger->info(message: sprintf('Added File Date in AssetResource: %s', $assetResource->getId()));
    }
}
