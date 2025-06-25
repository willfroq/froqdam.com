<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Document;

use Elastica\Exception\ExceptionInterface;
use Elastica\Exception\NotFoundException;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use JoliCode\Elastically\Client;
use Pimcore\Model\DataObject\ColourGuideline;
use Psr\Log\LoggerInterface;

final class DeleteColourGuidelineDocument
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ColourGuideline $colourGuideline): void
    {
        $index = $this->client->getIndex(IndexNames::ColourGuidelineItem->readable());

        if (!$index->exists()) {
            return;
        }

        $indexer = $this->client->getIndexer();

        $documentId = (string) $colourGuideline->getId();

        try {
            $index->getDocument($documentId);
        } catch (NotFoundException) {
            return;
        }

        $indexer->scheduleDelete(
            index: $index,
            id: $documentId,
        );

        try {
            $indexer->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
