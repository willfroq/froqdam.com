<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\MessageHandler;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\BuildSwitchUploadResponse;
use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Message\UploadFromSwitch;
use Froq\AssetBundle\Utility\ImplodeAssociativeArray;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'switch_upload', handles: UploadFromSwitch::class, method: '__invoke', priority: 10)]
final class UploadFromSwitchHandler
{
    public function __construct(
        private readonly BuildSwitchUploadResponse $buildSwitchUploadResponse,
        private readonly ApplicationLogger $applicationLogger,
        private readonly ImplodeAssociativeArray $implodeAssociativeArray,
        private readonly SendCriticalErrorEmail $sendCriticalErrorEmail
    ) {
    }

    /**
     * @throws Exception
     * @throws \Exception|TransportExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function __invoke(UploadFromSwitch $uploadFromSwitch): void
    {
        try {
            $response = ($this->buildSwitchUploadResponse)(
                new SwitchUploadRequest(
                    eventName: $uploadFromSwitch->eventName,
                    filename: $uploadFromSwitch->filename,
                    customerCode: $uploadFromSwitch->customerCode,
                    customAssetFolder: $uploadFromSwitch->customAssetFolder,
                    assetType: $uploadFromSwitch->assetType,
                    fileContents: new UploadedFile($uploadFromSwitch->temporaryFilePath, $uploadFromSwitch->filename),
                    assetResourceMetadataFieldCollection: ($uploadFromSwitch->assetResourceMetadataFieldCollection),
                    productData: $uploadFromSwitch->productData,
                    tagData: $uploadFromSwitch->tagData,
                    projectData: $uploadFromSwitch->projectData,
                    printerData: $uploadFromSwitch->printerData,
                    supplierData: $uploadFromSwitch->supplierData,
                    errors: []
                )
            );

            $this->applicationLogger->info(message: ($this->implodeAssociativeArray)($response->toArray()));
        } catch (\Exception $exception) {
            $this->applicationLogger->error(message: $exception->getMessage());

            ($this->sendCriticalErrorEmail)($uploadFromSwitch->filename);

            throw new \Exception(message: $exception->getMessage());
        }
    }
}
