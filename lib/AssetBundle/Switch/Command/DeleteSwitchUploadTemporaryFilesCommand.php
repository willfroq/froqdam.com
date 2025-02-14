<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Command;

use Doctrine\DBAL\Exception;
use Froq\PortalBundle\Repository\MessengerRepository;
use Pimcore\Console\AbstractCommand;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'froq:delete-switch-upload-temp-files',
    description: 'Delete switch upload temp files.',
    aliases: ['froq:delete-switch-upload-temp-files'],
    hidden: false
)]
final class DeleteSwitchUploadTemporaryFilesCommand extends AbstractCommand
{
    public function __construct(
        private readonly string $projectDirectory,
        private readonly ApplicationLogger $logger,
        private readonly MessengerRepository $messengerRepository,
        private readonly Filesystem $filesystem
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->messengerRepository->hasSwitchUploadQueued()) {
            $this->logger->info(message: "Can't delete switch upload temp files yet, there's still a switch_upload pending on the queue.");

            return Command::SUCCESS;
        }

        try {
            $finder = new Finder();
            $finder->in("$this->projectDirectory/public/var/tmp/uploads");

            foreach ($finder as $file) {
                $this->filesystem->remove($file->getRealPath());
            }
        } catch (IOExceptionInterface $exception) {
            $this->logger->error(message: $exception->getMessage(), context: ['component' => 'upload']);

            throw new IOException(message: $exception->getMessage() . 'DeleteSwitchUploadTemporaryFilesCommand.php line: '. __LINE__);
        }

        return Command::SUCCESS;
    }
}
