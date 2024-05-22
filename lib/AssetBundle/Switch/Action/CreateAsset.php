<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\AssetBundle\Switch\Enum\LogLevelNames;
use Froq\AssetBundle\Switch\Handlers\OrganizationFoldersErrorHandler;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreateAsset
{
    public function __construct(
        private readonly BuildFileAsset $buildFileAsset,
        private readonly BuildAssetResourceMetadata $buildAssetResourceMetadata,
        private readonly BuildProductFromPayload $buildProductFromPayload,
        private readonly BuildProjectFromPayload $buildProjectFromPayload,
        private readonly BuildPrinterFromPayload $buildPrinterFromPayload,
        private readonly BuildSupplierFromPayload $buildSupplierFromPayload,
        private readonly BuildTags $buildTags,
        private readonly OrganizationFoldersErrorHandler $organizationFoldersErrorHandler,
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function __invoke(
        UploadedFile $uploadedFile,
        SwitchUploadRequest $switchUploadRequest,
        Organization $organization,
        AssetType $assetType,
        string $assetFolderPath,
        float $start
    ): SwitchUploadResponse {
        ($this->organizationFoldersErrorHandler)($switchUploadRequest, $organization);

        $filename = $switchUploadRequest->filename;
        $asset = null;
        $assetFolderContainer = null;
        $newAssetVersionFolder = null;

        $assetFolder = Asset\Folder::getByPath($assetFolderPath);

        if ($assetFolder instanceof Asset\Folder) {
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
        }

        if (!($asset instanceof Asset)) {
            try {
                $asset?->delete(); /** @phpstan-ignore-line */
                $assetFolderContainer?->delete();
                $newAssetVersionFolder?->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = 'No Asset created. Make sure there is a file and it\'s not broken.';

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: ['component' => $switchUploadRequest->eventName]
            );

            return new SwitchUploadResponse(
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

        $actions[] = sprintf('Asset with ID %d is created and exists as: %s fullPath: %s', $asset->getId(), $asset->getPath(), $asset->getFullPath());

        $assetResourceMetadataFieldCollection = ($this->buildAssetResourceMetadata)($switchUploadRequest);

        $assetResourceFolderName = $switchUploadRequest->customAssetFolder;

        $rootAssetResourceFolder = $organization->getObjectFolder() . '/';

        $actions[] = sprintf('RootAssetResourceFolder %s', $rootAssetResourceFolder);

        $parentAssetResourceFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $assetResourceFolderName)
            ->addConditionParam('o_path = ?', $rootAssetResourceFolder)
            ->current();

        if (!($parentAssetResourceFolder instanceof DataObject)) {
            try {
                $asset->delete();
                $assetFolderContainer?->delete();
                $newAssetVersionFolder?->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('ParentAssetResourceFolder: key: %s and path: %s does not exist.', 'Assets', $rootAssetResourceFolder);

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: ['component' => $switchUploadRequest->eventName]
            );

            return new SwitchUploadResponse(
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

        $tags = ($this->buildTags)($switchUploadRequest, $organization, $actions);

        $assetFolderName = $switchUploadRequest->customAssetFolder;

        $parentAssetResource = AssetResource::create();
        $parentAssetResource->setPublished(true);
        $parentAssetResource->setPath("$rootAssetResourceFolder/$assetFolderName/$filename/");
        $parentAssetResource->setName($switchUploadRequest->filename);
        $parentAssetResource->setParentId((int)$parentAssetResourceFolder->getId());
        $parentAssetResource->setAssetType($assetType);
        $parentAssetResource->setAssetVersion(0);
        $parentAssetResource->setKey($switchUploadRequest->filename);
        $parentAssetResource->setMetadata($assetResourceMetadataFieldCollection);
        $parentAssetResource->setTags($tags);

        $parentAssetResource->save();

        $actions[] = sprintf('ParentAssetResource with ID %d is created %s', $parentAssetResource->getId(), $parentAssetResource->getPath());

        if (!($parentAssetResource instanceof AssetResource)) {
            try {
                $parentAssetResource->delete();
                $assetFolderContainer?->delete();
                $newAssetVersionFolder?->delete();
                $asset->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('ParentAssetResource %s does not exist.', $parentAssetResource);

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: ['component' => $switchUploadRequest->eventName]
            );

            return new SwitchUploadResponse(
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
        $assetResourceVersionOne->setTags($tags);

        $assetResourceVersionOne->save();

        $actions[] = sprintf('AssetResourceVersionOne with ID %d is created %s', $assetResourceVersionOne->getId(), $assetResourceVersionOne->getPath());

        if (!($assetResourceVersionOne instanceof AssetResource)) {
            try {
                $parentAssetResource->delete();
                $asset->delete();
                $assetFolderContainer?->delete();
                $newAssetVersionFolder?->delete();
                $assetResourceVersionOne->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('AssetResourceVersionOne %s does not exist.', $assetResourceVersionOne);

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(
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

        $existingAssetResources = $organization->getAssetResources();

        $organization->setAssetResources(array_values(array_unique([...$existingAssetResources, $parentAssetResource, $assetResourceVersionOne])));

        $organization->save();

        ($this->buildProductFromPayload)($switchUploadRequest, [$parentAssetResource, $assetResourceVersionOne], $organization, $actions);
        ($this->buildProjectFromPayload)($switchUploadRequest, [$parentAssetResource, $assetResourceVersionOne], $organization, $actions);
        ($this->buildPrinterFromPayload)($switchUploadRequest, $organization, $actions);
        ($this->buildSupplierFromPayload)($switchUploadRequest, $organization, $actions);

        $end = (float) microtime(true);
        $elapsed = $end - $start;
        $memUsage = memory_get_usage();
        $usage = ($memUsage / 1024) / 1024;
        $peakUsage = (memory_get_peak_usage() / 1024) / 1024;

        $this->logger->info(message: 'Asset Created' . implode(separator: ',', array: $actions), context: [
            'fileObject'    => 'Asset: ' . $asset->getId() . ': ' . $asset->getPath(),
            'relatedObject' => 'AssetResource: ' .  $assetResourceVersionOne->getId() . ': ' . $assetResourceVersionOne->getPath(),
            'component' => $switchUploadRequest->eventName
        ]);

        return new SwitchUploadResponse(
            eventName: $switchUploadRequest->eventName,
            date: date('F j, Y H:i'),
            logLevel: LogLevelNames::SUCCESS->name.': Asset Created! Upload workflow successfully finished',
            assetId: (string) $asset->getId(),
            assetResourceId: (string) $assetResourceVersionOne->getId(),
            relatedObjects: [
                'productId' => current($assetResourceVersionOne->getProducts()) instanceof Product ? current($assetResourceVersionOne->getProducts())->getId() : '',
                'projectId' => current($assetResourceVersionOne->getProjects()) instanceof Project ? current($assetResourceVersionOne->getProjects())->getId() : '',
            ],
            actions: $actions,
            statistics: [
                'Elapsed' => round($elapsed, 2) . ' seconds',
                'MemoryUsage' => round($usage, 2) . ' MB',
                'PeakMemoryUsage' => round($peakUsage, 2) . ' MB',
            ]
        );
    }
}
