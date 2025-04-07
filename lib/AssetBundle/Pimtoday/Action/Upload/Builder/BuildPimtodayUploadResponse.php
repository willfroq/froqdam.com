<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Upload\Builder;

use Exception;
use Froq\AssetBundle\Pimtoday\Action\CreateAssetResource;
use Froq\AssetBundle\Pimtoday\Action\UpdateAssetResource;
use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadResponse;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BuildPimtodayUploadResponse
{
    public function __construct(
        private readonly ApplicationLogger $logger,
        private readonly CreateAssetResource $createAssetResource,
        private readonly UpdateAssetResource $updateAssetResource,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(PimtodayUploadRequest $pimtodayUploadRequest): PimtodayUploadResponse
    {
        $organization = $pimtodayUploadRequest->organization;
        if (!($organization instanceof Organization)) {
            $message = 'Organization does not exist.';

            $this->logger->error(
                message: $message,
                context: [
                    'component' => 'pimtoday_upload'
                ]
            );

            throw new Exception(message: $message);
        }

        $uploadedFile = $pimtodayUploadRequest->fileContents;

        if (!($uploadedFile instanceof UploadedFile)) {
            $message = sprintf('File id: %s does not exist. Base64 not supported yet', $uploadedFile);

            $this->logger->error(
                message: $message,
                context: [
                    'component' => 'pimtoday_upload'
                ]
            );

            throw new Exception(message: $message);
        }

        $assetFolderPath = $organization->getAssetFolder() . '/';

        $assetFolder = Asset\Folder::getByPath($assetFolderPath);

        if (!($assetFolder instanceof Asset\Folder)) {
            $message = sprintf('Asset Folder id: %s does not exist.', $assetFolderPath);

            $this->logger->error(
                message: $message,
                context: [
                    'component' => 'pimtoday_upload'
                ]
            );

            throw new Exception(message: $message);
        }

        $assetResourceFolderPath = $organization->getObjectFolder() . '/';

        $parentAssetResource = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', $pimtodayUploadRequest->documentData?->documentName)
            ->addConditionParam('o_path = ?', "$assetResourceFolderPath".AssetResourceOrganizationFolderNames::Assets->readable().'/')
            ->current();

        $existingAssetResource = AssetResource::getById((int) $pimtodayUploadRequest->documentData?->damId);

        if (!($existingAssetResource instanceof AssetResource) && !($parentAssetResource instanceof AssetResource)) {
            return ($this->createAssetResource)(
                $assetFolder,
                $pimtodayUploadRequest,
                $assetFolderPath,
                $assetResourceFolderPath,
                $organization,
                $uploadedFile
            );
        }

        if (!($parentAssetResource instanceof AssetResource)) {
            $message = "$parentAssetResource parentAssetResource does not exist, make this folder in the admin!";

            $this->logger->error(message: $message, context: ['component' => 'pimtoday_upload']);

            throw new Exception(message: $message);
        }

        if (!($existingAssetResource instanceof AssetResource)) {
            $message = "$existingAssetResource existingAssetResource does not exist, update is impossible!";

            $this->logger->error(message: $message, context: ['component' => 'pimtoday_upload']);

            throw new Exception(message: $message);
        }

        return ($this->updateAssetResource)(
            $assetFolder,
            $pimtodayUploadRequest,
            $assetFolderPath,
            $assetResourceFolderPath,
            $parentAssetResource,
            $existingAssetResource,
            $organization,
            $uploadedFile
        );
    }
}
