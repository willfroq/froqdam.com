<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test:anything',
    description: 'hi.',
    aliases: ['test:anything'],
    hidden: false
)]
class PlaygroundCommand extends AbstractCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
