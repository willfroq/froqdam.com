<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Handlers;

use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\AssetBundle\Switch\Enum\LogLevelNames;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SwitchUploadRequestErrorHandler
{
    public function __construct(private readonly ApplicationLogger $logger, private readonly SendCriticalErrorEmail $sendCriticalErrorEmail)
    {
    }

    /** @param array<int, string> $actions */
    public function __invoke(
        SwitchUploadRequest $switchUploadRequest,
        ?Organization &$organization,
        ?AssetType &$assetType,
        ?SwitchUploadResponse &$switchUploadResponse,
        array $actions
    ): void {
        $organization = Organization::getByCode($switchUploadRequest->customerCode)->current(); /** @phpstan-ignore-line */
        if (!($organization instanceof Organization)) {
            $message = sprintf('Organization Code: %s does not exist.', $switchUploadRequest->customerCode);

            $actions[] = $message;

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: [
                    'component' => $switchUploadRequest->eventName
                ]
            );

            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

            $switchUploadResponse = new SwitchUploadResponse(
                eventName: $switchUploadRequest->eventName,
                date: date('F j, Y H:i'),
                logLevel: LogLevelNames::ERROR->name.": $message",
                assetId: '',
                assetResourceId: '',
                relatedObjects: [],
                actions: $actions,
                statistics: []
            );
        }

        if (!($switchUploadRequest->fileContents instanceof UploadedFile)) {
            $message = sprintf('File: %s is not a file.', $switchUploadRequest->fileContents);

            $actions[] = $message;

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: [
                    'component' => $switchUploadRequest->eventName
                ]
            );

            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

            $switchUploadResponse = new SwitchUploadResponse(
                eventName: $switchUploadRequest->eventName,
                date: date('F j, Y H:i'),
                logLevel: LogLevelNames::ERROR->name.": $message",
                assetId: '',
                assetResourceId: '',
                relatedObjects: [],
                actions: $actions,
                statistics: []
            );
        }

        $assetType = AssetType::getByName($switchUploadRequest->assetType)?->current(); /** @phpstan-ignore-line */
        if (!($assetType instanceof AssetType)) {
            $message = sprintf('%s is not an AssetType.', $assetType);

            $actions[] = $message;

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: [
                    'component' => $switchUploadRequest->eventName
                ]
            );

            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

            $switchUploadResponse = new SwitchUploadResponse(
                eventName: $switchUploadRequest->eventName,
                date: date('F j, Y H:i'),
                logLevel: LogLevelNames::ERROR->name.": $message",
                assetId: '',
                assetResourceId: '',
                relatedObjects: [],
                actions: $actions,
                statistics: []
            );
        }
    }
}
