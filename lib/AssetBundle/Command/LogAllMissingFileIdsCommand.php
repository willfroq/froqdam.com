<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'froq:log-missing-file-asset-resource-ids',
    description: 'Log missing file AssetResource ids in batches.',
    aliases: ['froq:log-missing-file-asset-resource-ids'],
    hidden: false
)]
final class LogAllMissingFileIdsCommand extends AbstractCommand
{
    private const BATCH_SIZE = 1000;

    public function __construct(private readonly ApplicationLogger $logger)
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $db = Db::get();
        $offset = 0;
        $missingFileIds = [];

        $date = new \DateTime();
        $logDate = $date->format('Y-m-d H:i:s');

        $totalCount = $db->fetchOne('SELECT COUNT(oo_id) FROM object_AssetResource');
        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();

        do {
            $assetResources = $db->fetchAllAssociative(
                sprintf('SELECT oo_id FROM object_AssetResource LIMIT %d OFFSET %d', self::BATCH_SIZE, $offset)
            );

            if (empty($assetResources)) {
                break;
            }

            foreach ($assetResources as $resourceRow) {
                $assetResource = AssetResource::getById($resourceRow['oo_id']);

                if (!$assetResource || $assetResource->hasChildren()) {
                    $progressBar->advance();
                    continue;
                }

                try {
                    $asset = $assetResource->getAsset();
                    $fileSize = (int)$asset?->getFileSize();

                    if ($fileSize === 0 || $asset === null) {
                        $missingFileIds[] = $assetResource->getId();
                    }
                } catch (\Exception $e) {
                    $missingFileIds[] = $assetResource->getId();
                    $progressBar->advance();
                    continue;
                }

                $progressBar->advance();
            }

            $offset += self::BATCH_SIZE;
        } while (true);

        $progressBar->finish();
        $output->writeln('');

        if (!empty($missingFileIds)) {
            $this->logger->warning(
                sprintf(
                    '[%s] Missing file AssetResource ids in batch: %s',
                    $logDate,
                    implode(',', $missingFileIds)
                )
            );
        }

        return Command::SUCCESS;
    }
}
