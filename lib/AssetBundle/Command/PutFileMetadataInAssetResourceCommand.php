<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Message\PutFileMetadataInAssetResourceMessage;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use Pimcore\Console\AbstractCommand;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'froq:put-file-metadata-in-asset-resource',
    description: 'Put file metadata in AssetResource.',
    aliases: ['froq:put-file-metadata-in-asset-resource'],
    hidden: false
)]
final class PutFileMetadataInAssetResourceCommand extends AbstractCommand
{
    public function __construct(
        private readonly ApplicationLogger $logger,
        private readonly AssetResourceRepository $assetResourceRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastId = 0;
        $batchSize = 50;

        $parentIdsCount = $this->assetResourceRepository->countParentIds();

        for ($i = 0; $i < $parentIdsCount; $i += $batchSize) {
            $parentIds = $this->assetResourceRepository->fetchParentIds($lastId, $batchSize);

            $this->messageBus->dispatch(new PutFileMetadataInAssetResourceMessage(parentIds: $parentIds));

            $lastId = (int) end($parentIds);

            $this->logger->info(message: sprintf('PutFileMetadataInAssetResourceMessage dispatched! Batch range: %s - %s Last Fetched Parent AR id: %s', $i, $parentIdsCount, $lastId));

            unset($parentIds);
        }

        return Command::SUCCESS;
    }
}
