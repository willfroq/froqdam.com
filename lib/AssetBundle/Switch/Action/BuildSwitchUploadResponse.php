<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BuildSwitchUploadResponse
{
    public function __construct(
        private readonly UpdateAsset $updateAsset,
        private readonly CreateAsset $createAsset,
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest): SwitchUploadResponse
    {
        $start = (float) microtime(true);

        $actions = [];

        $organization = Organization::getByCode($switchUploadRequest->customerCode)->current(); /** @phpstan-ignore-line */
        if (!($organization instanceof Organization)) {
            $message = sprintf('Organization Code: %s does not exist.', $switchUploadRequest->customerCode);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $uploadedFile = $switchUploadRequest->fileContents;

        if (!($uploadedFile instanceof UploadedFile)) {
            $message = sprintf('File: %s is not a file.', $uploadedFile);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $assetType = AssetType::getByName($switchUploadRequest->assetType)?->current(); /** @phpstan-ignore-line */
        if (!($assetType instanceof AssetType)) {
            $message = sprintf('%s is not an AssetType.', $assetType);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $filename = $switchUploadRequest->filename;

        $assetFolderPath = $organization->getAssetFolder() . '/';

        $assetFolderContainer = (new Asset\Listing())
            ->addConditionParam('path = ?', $assetFolderPath)
            ->addConditionParam('filename = ?', $filename)
            ->current();

        $existingAsset = (new Asset\Listing())
            ->addConditionParam('path = ?', $assetFolderPath . "$filename/1/")
            ->addConditionParam('filename = ?', $filename)
            ->current();

        if ($assetFolderContainer instanceof Folder && $existingAsset instanceof Asset) {
            return ($this->updateAsset)($assetFolderContainer, $existingAsset, $uploadedFile, $switchUploadRequest, $organization, $assetType, $start);
        }

        return ($this->createAsset)($uploadedFile, $switchUploadRequest, $organization, $assetType, $assetFolderPath, $start);
    }
}
