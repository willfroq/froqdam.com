<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Command;

use Doctrine\DBAL\Driver\Exception;
use Elastica\Document;
use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Mapper\BuildAssetResourceItemMapper;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use JoliCode\Elastically\Client;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elasticsearch:synchronously-create-asset-resource-index',
    description: 'Build new index from scratch and populate.',
    aliases: ['elasticsearch:create-index'],
    hidden: false
)]
final class CreateAssetResourceIndexSynchronousCommand extends Command
{
    public function __construct(
        private readonly Client $client,
        private readonly BuildAssetResourceItemMapper $buildAssetResourceMapper,
        private readonly AssetResourceRepository $assetResourceRepository,
    ) {
        parent::__construct();
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output, 50);

        $indexBuilder = $this->client->getIndexBuilder();
        $newIndex = $indexBuilder->createIndex(indexName: IndexNames::AssetResourceItem->readable());
        $indexer = $this->client->getIndexer();

        $parentIds = $this->assetResourceRepository->fetchParentIds(0, 5000);

        foreach ($parentIds as $parentId) {
            $parentAssetResource = AssetResource::getById($parentId);

            if (!($parentAssetResource instanceof AssetResource)) {
                continue;
            }

            $children = $parentAssetResource->getChildren();

            $assetResourceLatestVersion = end($children);

            if (!($assetResourceLatestVersion instanceof AssetResource)) {
                continue;
            }

            if (!($assetResourceLatestVersion->getAsset() instanceof Asset)) {
                continue;
            }

            $data = ($this->buildAssetResourceMapper)($parentAssetResource, $assetResourceLatestVersion);

            $indexer->scheduleIndex(
                index: $newIndex,
                document: new Document(
                    id: (string)$assetResourceLatestVersion->getId(),
                    data: $data
                )
            );

            $progressBar->advance();
        }

        $indexer->flush();

        $indexBuilder->markAsLive(index: $newIndex, indexName: IndexNames::AssetResourceItem->readable());
        $indexBuilder->speedUpRefresh(index: $newIndex);
        $indexBuilder->purgeOldIndices(indexName: IndexNames::AssetResourceItem->readable());

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
