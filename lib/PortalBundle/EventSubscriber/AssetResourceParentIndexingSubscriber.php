<?php

declare(strict_types=1);

namespace Froq\PortalBundle\EventSubscriber;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Event;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Youwe\PimcoreElasticsearchBundle\Elasticsearch\Index\SupportStrategy;
use Youwe\PimcoreElasticsearchBundle\Elasticsearch\Index\SupportStrategyInterface;
use Youwe\PimcoreElasticsearchBundle\Elasticsearch\IndexInterface;
use Youwe\PimcoreElasticsearchBundle\Message\DeleteElementMessage;
use Youwe\PimcoreElasticsearchBundle\Message\UpdateElementMessage;
use Youwe\PimcoreElasticsearchBundle\Service\IndexListingServiceInterface;
use Youwe\PimcoreElasticsearchBundle\Service\PimcoreElementService;

class AssetResourceParentIndexingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly IndexListingServiceInterface $esIndexListingManager,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event\DataObjectEvents::POST_UPDATE => 'updateParentIndex',
            Event\DataObjectEvents::POST_ADD => 'updateParentIndex',
            Event\DataObjectEvents::POST_DELETE => 'deleteParentIndex',
        ];
    }

    public function updateParentIndex(ElementEventInterface $event): void
    {
        $element = $event->getElement();
        if (!$element instanceof AssetResource) {
            return;
        }

        // Exit, because it will be handled by Youwe\PimcoreElasticsearchBundle\EventSubscriber\ElementChangeEventSubscriber
        if (AssetResourceHierarchyHelper::isParent($element)) {
            return;
        }

        $parent = $element->getParent();

        if ($parent && !$parent instanceof AssetResource) {
            return;
        }

        if (!is_object($parent)) {
            return;
        }

        foreach ($this->getIndexesSupportingType($parent) as $indexName => $index) {
            $elementService = $index->getElementService();

            if (!$elementService instanceof PimcoreElementService) {
                continue;
            }

            if ($index->getSupportStrategy()->isIndexed($parent)) {
                $message = new UpdateElementMessage(
                    $indexName,
                    $elementService->getIdentifier($parent)
                );
            } else {
                $message = new DeleteElementMessage(
                    $indexName,
                    $elementService->getIdentifier($parent)
                );
            }

            $this->messageBus->dispatch($message);
        }
    }

    public function deleteParentIndex(ElementEventInterface $event): void
    {
        $element = $event->getElement();
        if (!$element instanceof AssetResource) {
            return;
        }

        // Exit, because it will be handled by Youwe\PimcoreElasticsearchBundle\EventSubscriber\ElementChangeEventSubscriber
        if (AssetResourceHierarchyHelper::isParent($element)) {
            return;
        }

        $parent = $element->getParent();
        if (!$parent instanceof AssetResource) {
            return;
        }

        foreach ($this->getIndexesSupportingType($parent) as $indexName => $index) {
            $elementService = $index->getElementService();
            if (!$elementService instanceof PimcoreElementService) {
                continue;
            }

            $message = new DeleteElementMessage(
                $indexName,
                $elementService->getIdentifier($parent)
            );
            $this->messageBus->dispatch($message);
        }
    }

    /**
     * @return iterable<string, IndexInterface>
     */
    protected function getIndexesSupportingType(object $element): iterable
    {
        foreach ($this->esIndexListingManager->getIndexIdentifiers() as $indexIdentifier) {
            $index = $this->esIndexListingManager->getIndex($indexIdentifier);

            $supportStrategy = $index?->getSupportStrategy();

            if (!($supportStrategy instanceof SupportStrategyInterface)) {
                continue;
            }

            if (SupportStrategy::supportsType($supportStrategy, $element)) {
                yield $indexIdentifier => $index;
            }
        }
    }
}
