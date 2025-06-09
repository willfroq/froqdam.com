<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\EventSubscriber;

use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\Opensearch\Action\Document\CreateColourGuidelineDocument;
use Froq\PortalBundle\Opensearch\Action\Document\DeleteColourGuidelineDocument;
use Froq\PortalBundle\Opensearch\Action\Document\UpdateColourGuidelineDocument;
use Pimcore\Event;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Model\DataObject\ColourGuideline;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ColourGuidelineIndexingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CreateColourGuidelineDocument $createColourGuidelineDocument,
        private readonly DeleteColourGuidelineDocument $deleteColourGuidelineDocument,
        private readonly UpdateColourGuidelineDocument $updateColourGuidelineDocument,
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
        $colourGuideline = $event->getElement();

        if (!$colourGuideline instanceof ColourGuideline) {
            return;
        }

        ($this->updateColourGuidelineDocument)($colourGuideline);
    }

    /**
     * @throws ExceptionInterface
     */
    public function create(ElementEventInterface $event): void
    {
        $colourGuideline = $event->getElement();

        if (!$colourGuideline instanceof ColourGuideline) {
            return;
        }

        ($this->createColourGuidelineDocument)($colourGuideline);
    }

    /**
     * @throws ExceptionInterface
     */
    public function delete(ElementEventInterface $event): void
    {
        $colourGuideline = $event->getElement();

        if (!$colourGuideline instanceof ColourGuideline) {
            return;
        }

        ($this->deleteColourGuidelineDocument)($colourGuideline);
    }
}
