<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Handlers;

use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class SwitchUploadCriticalErrorHandler
{
    public function __construct(private readonly ApplicationLogger $logger, private readonly SendCriticalErrorEmail $sendCriticalErrorEmail)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest): void
    {
        $this->logger->error(
            message: sprintf(
                'Switch Upload Critical Error! Please reupload filename: %s , customer code: %s',
                $switchUploadRequest->filename,
                $switchUploadRequest->customerCode,
            ),
            context: [
                'component' => $switchUploadRequest->eventName
            ]
        );

        ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);
    }
}
