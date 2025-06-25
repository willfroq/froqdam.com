<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\MessageHandler\Command;

use Doctrine\DBAL\Driver\Exception;
use Elastica\Document;
use Froq\PortalBundle\Opensearch\Mapper\BuildAssetResourceItemMapper;
use Froq\PortalBundle\Opensearch\Message\AssetResourceIdsMessage;
use JoliCode\Elastically\Client;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'populate_assetresource_index', handles: AssetResourceIdsMessage::class, method: '__invoke', priority: 1)]
final class PopulateAssetResourceIndexHandler
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        private readonly BuildAssetResourceItemMapper $buildAssetResourceMapper,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(AssetResourceIdsMessage $parentAssetResourceIdsMessage): void
    {
        try {
            $index = $this->client->getIndex($parentAssetResourceIdsMessage->newIndexName);

            foreach ($parentAssetResourceIdsMessage->parentAssetResourceIds as $parentId) {
                $parentAssetResource = AssetResource::getById($parentId);

                if (!($parentAssetResource instanceof AssetResource)) {
                    continue;
                }

                $children = $parentAssetResource->getChildren();

                $assetResourceLatestVersion = end($children);

                if (!($assetResourceLatestVersion instanceof AssetResource)) {
                    continue;
                }

                $asset = $assetResourceLatestVersion->getAsset();

                if (!($asset instanceof Asset)) {
                    continue;
                }

                $index->addDocument(new Document(
                    id: (string) $assetResourceLatestVersion->getId(),
                    data: ($this->buildAssetResourceMapper)($parentAssetResource, $assetResourceLatestVersion)
                ));
            }
        } catch (\Exception $exception) {
            $this->logger->critical(
                message: $exception->getMessage().sprintf(
                    ' ParentAssetResourceIds: %s in indexName: %s FAILED! Please reindex!',
                    implode(',', $parentAssetResourceIdsMessage->parentAssetResourceIds),
                    $parentAssetResourceIdsMessage->newIndexName
                ),
                context: ['exception' => $exception]
            );

            throw new \Exception(message: $exception->getMessage());
        }
    }
}
