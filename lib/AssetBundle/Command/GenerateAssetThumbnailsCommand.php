<?php

namespace Froq\AssetBundle\Command;

use Froq\AssetBundle\Message\GenerateAssetThumbnailsMessage;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
class GenerateAssetThumbnailsCommand extends AbstractCommand
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('app:asset:generate-asset-thumbnails')
            ->setDescription('Generate thumbnails for Asset(s), useful to pre-generate thumbnails in the background')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'only create thumbnails of assets with this (IDs)'
            )
            ->addOption(
                'thumbnails',
                't',
                InputOption::VALUE_OPTIONAL,
                'only create specified thumbnails (comma separated eg.: thumb1,thumb2)'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'recreate thumbnails, regardless if they exist already'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $thumbnails = [];
        if ($input->getOption('thumbnails')) {
            $thumbnails = explode(',', $input->getOption('thumbnails'));

            foreach ($thumbnails as $thumbnail) {
                /** @var Image\Thumbnail\Config $thumbnailConfig */
                $thumbnailConfig = Image\Thumbnail\Config::getByName($thumbnail);
                if (!$thumbnailConfig) {
                    $this->writeError(sprintf('No Thumbnail with name=%s found', $thumbnail));

                    return self::FAILURE;
                }
            }
        }

        $assetIds = $input->getOption('id') ?? [];
        foreach ($assetIds as $id) {
            $asset = Asset::getById((int)$id);
            if (!$asset) {
                $this->writeError(sprintf('No Asset with ID=%s found', $id));

                return self::FAILURE;
            }
        }

        $items = $this->getItems($input);

        if (!$items) {
            $this->writeError('Nothing to generate');

            return self::SUCCESS;
        }

        $force = $input->getOption('force') ?: false;

        foreach ($items as $assetId) {
            $this->messageBus->dispatch(new GenerateAssetThumbnailsMessage($assetId, $thumbnails, $force));
        }

        $this->writeInfo(sprintf('%s Thumbnails will be generated in the background', count($items)));

        return self::SUCCESS;
    }

    /**
     * @return int[]
     */
    private function getItems(InputInterface $input): array
    {
        $list = new Asset\Listing();

        $list->setOrderKey('modificationDate');
        $list->setOrder('DESC');

        $conditionVariables = [];

        $conditions = ["(type = 'image' or type = 'document')"];

        if ($ids = $input->getOption('id')) {
            $conditions[] = sprintf('id in (%s)', implode(',', $ids));
        }

        $list->setCondition(implode(' AND ', $conditions), $conditionVariables);

        return $list->loadIdList();
    }
}
