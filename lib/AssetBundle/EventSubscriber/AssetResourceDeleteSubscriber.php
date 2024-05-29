<?php

declare(strict_types=1);

namespace Froq\AssetBundle\EventSubscriber;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Action\DeleteAssetAndAssetResourceContainer;
use Froq\AssetBundle\Action\DeleteAssetAndAssetResourceSpecificVersion;
use Froq\AssetBundle\Action\DeleteAssetAndAssetResourceVersionOne;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetResourceDeleteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly DeleteAssetAndAssetResourceContainer $deleteAssetAndAssetResourceContainer,
        private readonly DeleteAssetAndAssetResourceVersionOne $deleteAssetAndAssetResourceVersionOne,
        private readonly DeleteAssetAndAssetResourceSpecificVersion $deleteAssetAndAssetResourceSpecificVersion,
    ) {
    }

    /** @return string[] */
    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_DELETE => 'onPreDelete',
        ];
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function onPreDelete(DataObjectEvent $event): void
    {
        $assetResource = $event->getObject();

        if (!($assetResource instanceof AssetResource)) {
            return;
        }

        ($this->deleteAssetAndAssetResourceContainer)($assetResource);

        ($this->deleteAssetAndAssetResourceVersionOne)($assetResource);

        ($this->deleteAssetAndAssetResourceSpecificVersion)($assetResource);
    }
}
