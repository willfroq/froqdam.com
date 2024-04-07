<?php

declare(strict_types=1);

namespace Froq\AssetBundle\EventSubscriber;

use Froq\AssetBundle\Manager\AssetResource\AssetResourcePostUpdateManager;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetResourceUpdateSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly AssetResourcePostUpdateManager $assetResourcePostUpdateManager)
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::POST_UPDATE => 'onPostUpdate',
        ];
    }

    /**
     * @param DataObjectEvent $event
     *
     * @return void
     *
     * @throws \Exception
     */
    public function onPostUpdate(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if ($object instanceof AssetResource) {
            $this->assetResourcePostUpdateManager->handlePostUpdated($object);
        }
    }
}
