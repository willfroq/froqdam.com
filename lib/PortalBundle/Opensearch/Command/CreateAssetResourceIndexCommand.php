<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Command;

use Doctrine\DBAL\Driver\Exception;
use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Message\AssetResourceIdsMessage;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use JoliCode\Elastically\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'elasticsearch:async-create-assetresource-index',
    description: 'Build new index from scratch and populate.',
    aliases: ['elasticsearch:async-create-index'],
    hidden: false
)]
final class CreateAssetResourceIndexCommand extends Command
{
    public function __construct(
        private readonly Client $client,
        private readonly AssetResourceRepository $assetResourceRepository,
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $messageBus,
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

        $lastId = 0;
        $batchSize = 100;

        $parentIdsCount = $this->assetResourceRepository->countParentIds();

        for ($i = 0; $i < $parentIdsCount; $i += $batchSize) {
            $parentIds = $this->assetResourceRepository->fetchParentIds($lastId, $batchSize);

            $this->messageBus->dispatch(new AssetResourceIdsMessage(parentAssetResourceIds: $parentIds, newIndexName: $newIndex->getName()));

            $message = sprintf('AssetResourceIdsMessage dispatched! Batch range: %s - %s Last Fetched AssetResource id: %s', $i, $parentIdsCount, $lastId);

            $lastId = (int) end($parentIds);

            $this->logger->info(message: $message);

            $output->writeln($message);

            unset($parentIds);
        }

        $indexer->flush();

        $indexBuilder->markAsLive(index: $newIndex, indexName: IndexNames::AssetResourceItem->readable());
        $indexBuilder->speedUpRefresh(index: $newIndex);

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
