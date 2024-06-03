<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\AssetBundle\Switch\Handlers\SwitchUploadRequestErrorHandler;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class BuildSwitchUploadResponse
{
    public function __construct(
        private readonly UpdateAsset $updateAsset,
        private readonly CreateAsset $createAsset,
        private readonly BuildOrganizationObjectFolderIfNotExists $buildOrganizationObjectFolderIfNotExists,
        private readonly BuildOrganizationAssetFolderIfNotExists $buildOrganizationAssetFolderIfNotExists,
        private readonly LinkAssetResourceFolder $linkAssetResourceFolder,
        private readonly SwitchUploadRequestErrorHandler $switchUploadRequestErrorHandler,
        private readonly SendCriticalErrorEmail $sendCriticalErrorEmail
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception|TransportExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest): SwitchUploadResponse
    {
        $start = (float) microtime(true);

        $actions = [];
        $organization = null;
        $errorResponse = null;
        $assetType = null;

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $switchUploadRequest->fileContents;

        ($this->switchUploadRequestErrorHandler)($switchUploadRequest, $organization, $assetType, $errorResponse, $actions);

        if ($errorResponse instanceof SwitchUploadResponse ||
            !($organization instanceof Organization) ||
            !($assetType instanceof AssetType)
        ) {
            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

            return $errorResponse;
        }

        $filename = $switchUploadRequest->filename;

        ($this->linkAssetResourceFolder)($organization);

        ($this->buildOrganizationObjectFolderIfNotExists)($organization);

        ($this->buildOrganizationAssetFolderIfNotExists)($organization, $filename);

        $assetFolderPath = $organization->getAssetFolder() . '/';

        $assetFolderContainer = (new Asset\Listing())
            ->addConditionParam('path = ?', $assetFolderPath)
            ->addConditionParam('filename = ?', $filename)
            ->current();

        $existingAsset = (new Asset\Listing())
            ->addConditionParam('path = ?', $assetFolderPath . "$filename/1/")
            ->addConditionParam('filename = ?', $filename)
            ->current();

        if ($assetFolderContainer instanceof Folder && $existingAsset instanceof Asset && $uploadedFile instanceof UploadedFile) {
            return ($this->updateAsset)($assetFolderContainer, $existingAsset, $uploadedFile, $switchUploadRequest, $organization, $assetType, $start);
        }

        return ($this->createAsset)($uploadedFile, $switchUploadRequest, $organization, $assetType, $assetFolderPath, $start);
    }
}
