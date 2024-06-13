<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class DeleteTemporaryFile
{
    public function __construct(
        private readonly ApplicationLogger $applicationLogger,
        private readonly Filesystem $filesystem,
        private readonly SendCriticalErrorEmail $sendCriticalErrorEmail
    ) {
    }

    /**
     * @throws Exception|TransportExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function __invoke(string $filePath): void
    {
        try {
            if ($this->filesystem->exists($filePath)) {
                $this->filesystem->remove($filePath);
            }
        } catch (IOExceptionInterface $exception) {
            $this->applicationLogger->error(message: $exception->getMessage(), context: ['component' => 'upload']);

            ($this->sendCriticalErrorEmail)($filePath);

            throw new IOException(message: $exception->getMessage());
        }
    }
}
