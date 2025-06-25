<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\EventSubscriber;

use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\AssetLibrary\Document\CreateAssetResourceDocument;
use Froq\PortalBundle\AssetLibrary\Document\DeleteAssetResourceDocument;
use Froq\PortalBundle\AssetLibrary\Document\UpdateAssetResourceDocument;
use Pimcore\Event;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetResourceIndexingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CreateAssetResourceDocument $createAssetResourceDocument,
        private readonly DeleteAssetResourceDocument $deleteAssetResourceDocument,
        private readonly UpdateAssetResourceDocument $updateAssetResourceDocument,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event\DataObjectEvents::POST_UPDATE => 'update',
            Event\DataObjectEvents::POST_ADD => 'create',
            Event\DataObjectEvents::POST_DELETE => 'delete',
        ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function update(ElementEventInterface $event): void
    {
        $assetResource = $event->getElement();

        if (!$assetResource instanceof AssetResource) {
            return;
        }

        ($this->updateAssetResourceDocument)($assetResource);
    }

    /**
     * @throws ExceptionInterface
     */
    public function create(ElementEventInterface $event): void
    {
        $assetResource = $event->getElement();

        if (!$assetResource instanceof AssetResource) {
            return;
        }

        ($this->createAssetResourceDocument)($assetResource);
    }

    /**
     * @throws ExceptionInterface
     */
    public function delete(ElementEventInterface $event): void
    {
        $assetResource = $event->getElement();

        if (!$assetResource instanceof AssetResource) {
            return;
        }

        ($this->deleteAssetResourceDocument)($assetResource);
    }
}
