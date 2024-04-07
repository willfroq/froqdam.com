<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Action;

use Froq\AssetBundle\Action\BuildFileAsset;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadResponse;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreateAsset
{
    public function __construct(
        private readonly BuildFileAsset $buildFileAsset,
        private readonly BuildAssetResourceMetadata $buildAssetResourceMetadata,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        UploadedFile $uploadedFile,
        SwitchUploadRequest $switchUploadRequest,
        Organization $organization,
        string $assetFolderPath,
        string $filename,
        float $start
    ): SwitchUploadResponse {
        $assetFolder = Asset\Folder::getByPath($assetFolderPath);

        $assetFolderContainer = new Asset\Folder();
        $assetFolderContainer->setParent($assetFolder);
        $assetFolderContainer->setFilename($filename);
        $assetFolderContainer->setPath($assetFolderPath);
        $assetFolderContainer->save();

        $newAssetVersionFolder = new Asset\Folder();
        $newAssetVersionFolder->setParent($assetFolderContainer);
        $newAssetVersionFolder->setFilename('1');
        $newAssetVersionFolder->setPath($assetFolderPath."$filename/");
        $newAssetVersionFolder->save();

        $actions[] = sprintf('NewAssetVersionFolder with ID %d is created with path: %s', $newAssetVersionFolder->getId(), $newAssetVersionFolder->getPath());

        $asset = ($this->buildFileAsset)($uploadedFile, $filename, $newAssetVersionFolder);

        if ($asset === null) {
            $actions[] = 'No Asset created. Make sure there is a file and it\'s not broken.';

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $actions[] = sprintf('Asset with ID %d is created and exists as: %s fullPath: %s', $asset->getId(), $asset->getPath(), $asset->getFullPath());

        $assetResourceMetadataFieldCollection = ($this->buildAssetResourceMetadata)($switchUploadRequest, $asset);

        $assetType = AssetType::getByName($switchUploadRequest->assetType)?->current(); /** @phpstan-ignore-line */
        if (!($assetType instanceof AssetType)) {
            $actions[] = sprintf('%s is not an AssetType.', $assetType);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $rootAssetResourceFolder = $organization->getObjectFolder() . '/';

        $actions[] = sprintf('RootAssetResourceFolder %s', $rootAssetResourceFolder);

        $parentAssetResourceFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', 'Assets')
            ->addConditionParam('o_path = ?', $rootAssetResourceFolder)
            ->current();

        if (!($parentAssetResourceFolder instanceof DataObject)) {
            $actions[] = sprintf('ParentAssetResourceFolder: key: %s and path: %s does not exist.', 'Assets', $rootAssetResourceFolder);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $parentAssetResource = AssetResource::create();
        $parentAssetResource->setPublished(true);
        $parentAssetResource->setPath($rootAssetResourceFolder . $switchUploadRequest->customAssetFolder . '/');
        $parentAssetResource->setName($switchUploadRequest->filename);
        $parentAssetResource->setParentId((int)$parentAssetResourceFolder->getId());
        $parentAssetResource->setAsset($asset);
        $parentAssetResource->setAssetType($assetType);
        $parentAssetResource->setAssetVersion(0);
        $parentAssetResource->setKey($switchUploadRequest->filename);
        $parentAssetResource->setMetadata($assetResourceMetadataFieldCollection);

        $parentAssetResource->save();

        $actions[] = sprintf('ParentAssetResource with ID %d is created %s', $parentAssetResource->getId(), $parentAssetResource->getPath());

        if (!($parentAssetResource instanceof AssetResource)) {
            $actions[] = sprintf('ParentAssetResource %s does not exist.', $parentAssetResource);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $assetResourceVersionOne = AssetResource::create();
        $assetResourceVersionOne->setPublished(true);
        $assetResourceVersionOne->setPath($parentAssetResource->getPath().$parentAssetResource->getKey().'/');
        $assetResourceVersionOne->setName($switchUploadRequest->filename);
        $assetResourceVersionOne->setParentId((int) $parentAssetResource->getId());
        $assetResourceVersionOne->setAsset($asset);
        $assetResourceVersionOne->setAssetType($assetType);
        $assetResourceVersionOne->setAssetVersion(1);
        $assetResourceVersionOne->setKey('1');
        $assetResourceVersionOne->setMetadata($assetResourceMetadataFieldCollection);

        $assetResourceVersionOne->save();

        $actions[] = sprintf('AssetResourceVersionOne with ID %d is created %s', $assetResourceVersionOne->getId(), $assetResourceVersionOne->getPath());

        if (!($assetResourceVersionOne instanceof AssetResource)) {
            $actions[] = sprintf('AssetResourceVersionOne %s does not exist.', $assetResourceVersionOne);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $end = (float) microtime(true);
        $elapsed = $end - $start;
        $memUsage = memory_get_usage();
        $usage = ($memUsage / 1024) / 1024;
        $peakUsage = (memory_get_peak_usage() / 1024) / 1024;

        return new SwitchUploadResponse(
            eventName: $switchUploadRequest->eventName,
            date: date('F j, Y H:i'),
            actions: $actions,
            statistics: [
                'Elapsed' => round($elapsed, 2) . ' seconds',
                'MemoryUsage' => round($usage, 2) . ' MB',
                'PeakMemoryUsage' => round($peakUsage, 2) . ' MB',
            ]
        );
    }
}
