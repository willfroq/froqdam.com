<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Document;

use Elastica\Document as ElasticaDocument;
use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Mapper\BuildAssetResourceItemMapper;
use JoliCode\Elastically\Client;
use Pimcore\Model\DataObject\AssetResource;
use Psr\Log\LoggerInterface;

final class CreateAssetResourceDocument
{
    public function __construct(
        private readonly Client $client,
        private readonly BuildAssetResourceItemMapper $buildAssetResourceItemMapper,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function __invoke(AssetResource $assetResource): void
    {
        $index = $this->client->getIndex(IndexNames::AssetResourceItem->readable());

        if (!$index->exists()) {
            return;
        }

        $indexer = $this->client->getIndexer();

        $parentAssetResource = $assetResource->getParent();

        if (!($parentAssetResource instanceof AssetResource)) {
            return;
        }

        $data = ($this->buildAssetResourceItemMapper)($parentAssetResource, $assetResource);

        $assetResourceId = (string) $assetResource->getId();

        if (empty($assetResourceId)) {
            return;
        }

        $indexer->scheduleCreate(
            index: $index,
            document: new ElasticaDocument(
                id: $assetResourceId,
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
