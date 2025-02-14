<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Carbon\Carbon;
use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Action\GetFileDateFromEmbeddedMetadata;
use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Froq\AssetBundle\Switch\Action\RelatedObject\BuildAssetResourceFolderIfNotExists;
use Froq\AssetBundle\Switch\Action\RelatedObject\BuildShapeCode;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\LogLevelNames;
use Froq\AssetBundle\Switch\Handlers\OrganizationFoldersErrorHandler;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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
        private readonly SendCriticalErrorEmail $sendCriticalErrorEmail,
        private readonly BuildShapeCode $buildShapeCode,
        private readonly BuildAssetResourceFolderIfNotExists $buildAssetResourceFolderIfNotExists,
        private readonly GetFileDateFromEmbeddedMetadata $getFileDateFromEmbeddedMetadata
    ) {
    }

    /**
     * @throws \Exception
     * @throws Exception|TransportExceptionInterface
     * @throws TransportExceptionInterface
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

        $assetFolderContainer = Asset\Folder::getByPath($assetFolderPath.$filename);
        $newAssetVersionFolder = Asset\Folder::getByPath($assetFolderPath."$filename/1");
        $assetFolder = Asset\Folder::getByPath($assetFolderPath);

        if (!($assetFolderContainer instanceof Asset\Folder)) {
            $assetFolderContainer = new Asset\Folder();
        }

        if (!($newAssetVersionFolder instanceof Asset\Folder)) {
            $newAssetVersionFolder = new Asset\Folder();
        }

        if ($assetFolder instanceof Asset\Folder) {
            $assetFolderContainer->setParent($assetFolder);
            $assetFolderContainer->setFilename($filename);
            $assetFolderContainer->setPath($assetFolderPath);
            $assetFolderContainer->save();

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
                $assetFolderContainer->delete();
                $newAssetVersionFolder->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage() . 'CreateAsset.php line:'. __LINE__);
            }

            $message = 'CreateAsset line:'. __LINE__ .' No Asset created. Make sure there is a file and it\'s not broken.';

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: ['component' => $switchUploadRequest->eventName]
            );

            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

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

        $rootAssetResourceFolder = $organization->getObjectFolder() . '/';

        $actions[] = sprintf('RootAssetResourceFolder %s', $rootAssetResourceFolder);

        $customAssetFolder = $switchUploadRequest->customAssetFolder;

        $parentAssetResourceFolder = ($this->buildAssetResourceFolderIfNotExists)($organization, (string) $customAssetFolder);

        $tags = ($this->buildTags)($switchUploadRequest, $organization, $actions);

        try {
            $fileModifyDate = new Carbon(time: (($this->getFileDateFromEmbeddedMetadata)($asset))?->modifyDate);
            $fileCreateDate = new Carbon(time: (($this->getFileDateFromEmbeddedMetadata)($asset))?->createDate);
        } catch (\Exception $exception) {
            $this->logger->error(
                message: sprintf('Create Asset line:'. __LINE__ . ' %s has invalid date string format from file.', $asset),
                context: ['component' => $switchUploadRequest->eventName]
            );

            throw new \Exception(message: $exception->getMessage() . 'CreateAsset.php line: ' . __LINE__);
        }

        $parentAssetResource = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', $switchUploadRequest->filename)
            ->addConditionParam('o_path = ?', "$rootAssetResourceFolder/$customAssetFolder/$filename/")
            ->current();

        if (!($parentAssetResource instanceof AssetResource)) {
            $parentAssetResource = AssetResource::create();
        }

        $parentAssetResource->setPublished(true);
        $parentAssetResource->setPath("$rootAssetResourceFolder/$customAssetFolder/$filename/");
        $parentAssetResource->setName($switchUploadRequest->filename);
        $parentAssetResource->setParentId((int) $parentAssetResourceFolder->getId());
        $parentAssetResource->setAssetType($assetType);
        $parentAssetResource->setAssetVersion(0);
        $parentAssetResource->setKey($switchUploadRequest->filename);
        $parentAssetResource->setFileModifyDate($fileModifyDate);
        $parentAssetResource->setFileCreateDate($fileCreateDate);

        $parentAssetResource->save();

        $actions[] = sprintf('ParentAssetResource with ID %d is created %s', $parentAssetResource->getId(), $parentAssetResource->getPath());

        if (!($parentAssetResource instanceof AssetResource)) {
            try {
                $parentAssetResource->delete();
                $assetFolderContainer->delete();
                $newAssetVersionFolder->delete();
                $asset->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage() . 'CreateAsset.php line:'. __LINE__);
            }

            $message = sprintf('ParentAssetResource %s does not exist.', $parentAssetResource);

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(
                message: $message . implode(separator: ',', array: $actions),
                context: ['component' => $switchUploadRequest->eventName]
            );

            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

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

        $assetResourceVersionOne = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', '1')
            ->addConditionParam('o_path = ?', $parentAssetResource->getPath().$parentAssetResource->getKey().'/')
            ->current();

        if (!($assetResourceVersionOne instanceof AssetResource)) {
            $assetResourceVersionOne = AssetResource::create();
        }

        $assetResourceVersionOne->setPublished(true);
        $assetResourceVersionOne->setPath($parentAssetResource->getPath().$parentAssetResource->getKey().'/');
        $assetResourceVersionOne->setName($switchUploadRequest->filename);
        $assetResourceVersionOne->setParentId((int) $parentAssetResource->getId());
        $assetResourceVersionOne->setAsset($asset);
        $assetResourceVersionOne->setAssetType($assetType);
        $assetResourceVersionOne->setAssetVersion(1);
        $assetResourceVersionOne->setMetadata($assetResourceMetadataFieldCollection);
        $assetResourceVersionOne->setKey('1');
        $assetResourceVersionOne->setTags($tags);
        $assetResourceVersionOne->setFileModifyDate($fileModifyDate);
        $assetResourceVersionOne->setFileCreateDate($fileCreateDate);

        $assetResourceVersionOne->save();

        $actions[] = sprintf('AssetResourceVersionOne with ID %d is created %s', $assetResourceVersionOne->getId(), $assetResourceVersionOne->getPath());

        if (!($assetResourceVersionOne instanceof AssetResource)) {
            try {
                $parentAssetResource->delete();
                $asset->delete();
                $assetFolderContainer->delete();
                $newAssetVersionFolder->delete();
                $assetResourceVersionOne->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage() . 'CreateAsset.php line:'. __LINE__);
            }

            $message = sprintf('AssetResourceVersionOne %s does not exist.', $assetResourceVersionOne);

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            ($this->sendCriticalErrorEmail)($switchUploadRequest->filename);

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

        if ((AssetResourceOrganizationFolderNames::Packshots->readable() === $customAssetFolder ||
                AssetResourceOrganizationFolderNames::Assets->readable() === $customAssetFolder)
            && $assetResourceMetadataFieldCollection instanceof Fieldcollection) {
            ($this->buildShapeCode)($assetResourceMetadataFieldCollection, $parentAssetResource);
        }

        $existingAssetResources = $organization->getAssetResources();

        $organization->setAssetResources(array_values(array_unique([...$existingAssetResources, $parentAssetResource])));

        $organization->save();

        ($this->buildProductFromPayload)($switchUploadRequest, $parentAssetResource, $organization, $actions);
        ($this->buildProjectFromPayload)($switchUploadRequest, $parentAssetResource, $organization, $actions);
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
