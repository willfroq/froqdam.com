<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

final class DeleteTemporaryFile
{
    public function __construct(
        private readonly ApplicationLogger $applicationLogger,
        private readonly Filesystem $filesystem
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(string $filePath): void
    {
        try {
            if (!$this->filesystem->exists($filePath)) {
                $this->filesystem->remove($filePath);
            }
        } catch (IOExceptionInterface $exception) {
            $this->applicationLogger->error(message: $exception->getMessage(), context: ['component' => 'upload']);

            throw new IOException(message: $exception->getMessage());
        }
    }
}
