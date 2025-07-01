<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Command;

use Elastica\Document;
use Elastica\Exception\ExceptionInterface;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Mapper\BuildColourGuidelineItemMapper;
use JoliCode\Elastically\Client;
use Pimcore\Model\DataObject\ColourGuideline;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elasticsearch:synchronous-create-colour-guideline-index',
    description: 'Build new colour guideline index from scratch and populate.',
    aliases: ['elasticsearch:synchronous-create-colour-guideline-index'],
    hidden: false
)]
final class CreateProjectIndexSynchronousCommand extends Command
{
    public function __construct(
        private readonly Client $client,
        private readonly BuildColourGuidelineItemMapper $buildColourGuidelineItemMapper,
    ) {
        parent::__construct();
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output, 50);

        $indexBuilder = $this->client->getIndexBuilder();
        $newIndex = $indexBuilder->createIndex(indexName: IndexNames::ColourGuidelineItem->readable());
        $indexer = $this->client->getIndexer();

        $colourGuidelineList = (new ColourGuideline\Listing());

        foreach ($colourGuidelineList as $colourGuideline) {
            if (!($colourGuideline instanceof ColourGuideline)) {
                continue;
            }

            $indexer->scheduleIndex(
                index: $newIndex,
                document: new Document(
                    id: (string) $colourGuideline->getId(),
                    data: ($this->buildColourGuidelineItemMapper)($colourGuideline)
                )
            );

            $progressBar->advance();
        }

        $indexer->flush();

        $indexBuilder->markAsLive(index: $newIndex, indexName: IndexNames::ColourGuidelineItem->readable());
        $indexBuilder->speedUpRefresh(index: $newIndex);
        $indexBuilder->purgeOldIndices(indexName: IndexNames::ColourGuidelineItem->readable());

        $output->writeln(sprintf(' %s index created!', $newIndex->getName()));

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
