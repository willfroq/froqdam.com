<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Carbon\Carbon;
use Froq\AssetBundle\Action\GetFileDateFromEmbeddedMetadata;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'froq:fill-in-asset-resource-file-created-and-modified-date',
    description: 'Fill in AssetResource file created and modified date.',
    aliases: ['froq:fill-in-asset-resource-file-created-and-modified-date'],
    hidden: false
)]
final class FillInAssetResourceFileCreatedAndModifiedDate extends AbstractCommand
{
    private const BATCH_SIZE = 1000;

    public function __construct(
        private readonly ApplicationLogger $logger,
        private readonly GetFileDateFromEmbeddedMetadata $getFileDateFromEmbeddedMetadata)
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $db = Db::get();
        $offset = 0;

        $totalCount = $db->fetchOne('SELECT COUNT(oo_id) FROM object_AssetResource WHERE fileCreateDate IS NULL');
        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();

        do {
            $assetResources = $db->fetchAllAssociative(
                sprintf('SELECT oo_id FROM object_AssetResource WHERE fileCreateDate IS NULL LIMIT %d OFFSET %d', self::BATCH_SIZE, $offset)
            );

            if (empty($assetResources)) {
                break;
            }

            foreach ($assetResources as $resourceRow) {
                try {
                    $assetResource = AssetResource::getById($resourceRow['oo_id']);

                    if (!$assetResource || $assetResource->hasChildren()) {
                        $progressBar->advance();
                        continue;
                    }

                    $asset = $assetResource->getAsset();
                    if (!($asset instanceof Asset)) {
                        $progressBar->advance();
                        continue;
                    }

                    $isUpdated = false;

                    $fileCreateDate = (($this->getFileDateFromEmbeddedMetadata)($asset))?->createDate;
                    if ($fileCreateDate && empty($assetResource->getFileCreateDate())) {
                        $assetResource->setFileCreateDate(new Carbon(time: $fileCreateDate));
                        $isUpdated = true;
                    }

                    $fileModifyDate = ($this->getFileDateFromEmbeddedMetadata)($asset)?->modifyDate;
                    if ($fileModifyDate && empty($assetResource->getFileModifyDate())) {
                        $assetResource->setFileModifyDate(new Carbon(time: $fileModifyDate));
                        $isUpdated = true;
                    }

                    if ($isUpdated) {
                        $assetResource->save();
                    }
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
                $progressBar->advance();
            }

            $offset += self::BATCH_SIZE;

        } while (true);

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
