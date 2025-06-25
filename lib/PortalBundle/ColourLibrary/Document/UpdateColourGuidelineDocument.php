<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Document;

use Elastica\Document as ElasticaDocument;
use Elastica\Exception\ExceptionInterface;
use Elastica\Exception\NotFoundException;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Mapper\BuildColourGuidelineItemMapper;
use JoliCode\Elastically\Client;
use Pimcore\Model\DataObject\ColourGuideline;
use Psr\Log\LoggerInterface;

final class UpdateColourGuidelineDocument
{
    public function __construct(
        private readonly Client $client,
        private readonly BuildColourGuidelineItemMapper $buildColourGuidelineItemMapper,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ColourGuideline $colourGuideline): void
    {
        $index = $this->client->getIndex(IndexNames::ColourGuidelineItem->readable());

        if (!$index->exists()) {
            return;
        }

        $indexer = $this->client->getIndexer();

        $data = ($this->buildColourGuidelineItemMapper)($colourGuideline);

        $documentId = (string) $colourGuideline->getId();

        if (empty($documentId)) {
            return;
        }

        try {
            $index->getDocument($documentId);
        } catch (NotFoundException) {
            return;
        }

        $indexer->scheduleUpdate(
            index: $index,
            document: new ElasticaDocument(
                id: $documentId,
                data: $data
            )
        );

        try {
            $indexer->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
