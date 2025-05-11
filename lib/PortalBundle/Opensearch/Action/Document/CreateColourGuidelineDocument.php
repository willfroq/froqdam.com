<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Document;

use Elastica\Document as ElasticaDocument;
use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Mapper\BuildColourGuidelineItemMapper;
use JoliCode\Elastically\Client;
use Pimcore\Model\DataObject\ColourGuideline;
use Psr\Log\LoggerInterface;

final class CreateColourGuidelineDocument
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

        $colourGuidelineId = (string) $colourGuideline->getId();

        if (empty($colourGuidelineId)) {
            return;
        }

        $indexer->scheduleCreate(
            index: $index,
            document: new ElasticaDocument(
                id: $colourGuidelineId,
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
