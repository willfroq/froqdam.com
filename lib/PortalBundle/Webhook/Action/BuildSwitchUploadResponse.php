<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Action;

use Exception;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadResponse;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BuildSwitchUploadResponse
{
    public function __construct(private readonly UpdateAsset $updateAsset, private readonly CreateAsset $createAsset)
    {
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
            $actions[] = sprintf('Organization Code: %s does not exist.', $switchUploadRequest->customerCode);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $uploadedFile = $switchUploadRequest->fileContents;

        if (!($uploadedFile instanceof UploadedFile)) {
            $actions[] = sprintf('File: %s is not a file.', $uploadedFile);

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
            return ($this->updateAsset)($assetFolderContainer, $existingAsset, $uploadedFile, $switchUploadRequest, $organization, $filename, $start);
        }

        return ($this->createAsset)($uploadedFile, $switchUploadRequest, $organization, $assetFolderPath, $filename, $start);
    }
}
