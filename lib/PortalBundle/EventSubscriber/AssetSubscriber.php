<?php

declare(strict_types=1);

namespace Froq\PortalBundle\EventSubscriber;

use Froq\AssetBundle\Message\GenerateAssetThumbnailsMessage;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Model\Asset;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AssetSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AssetEvents::POST_ADD => 'onSave',
            AssetEvents::POST_UPDATE => 'onSave',
        ];
    }

    /**
     * @param AssetEvent $event
     *
     * @return void
     *
     * @throws \Exception
     */
    public function onSave(AssetEvent $event): void
    {
        $asset = $event->getAsset();

        if ($asset instanceof Asset) {
            $this->messageBus->dispatch(new GenerateAssetThumbnailsMessage((int) $asset->getId(), ['portal_asset_detail_preview', 'portal_asset_library_item']));
        }
    }
}
