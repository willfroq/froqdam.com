<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\LogLevelNames;
use Froq\AssetBundle\Switch\Handlers\OrganizationFoldersErrorHandler;
use Froq\PortalBundle\Repository\AssetRepository;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class UpdateAsset
{
    public function __construct(
        private readonly BuildFileAsset $buildFileAsset,
        private readonly BuildAssetResourceMetadata $buildAssetResourceMetadata,
        private readonly AssetRepository $assetRepository,
        private readonly AssetResourceRepository $assetResourceRepository,
        private readonly BuildTags $buildTags,
        private readonly OrganizationFoldersErrorHandler $organizationFoldersErrorHandler,
        private readonly ApplicationLogger $logger,
        private readonly SendCriticalErrorEmail $sendCriticalErrorEmail,
        private readonly BuildProductFromPayload $buildProductFromPayload,
        private readonly BuildProjectFromPayload $buildProjectFromPayload,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception|TransportExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function __invoke(
        Asset $assetFolderContainer,
        Asset $existingAsset,
        UploadedFile $uploadedFile,
        SwitchUploadRequest $switchUploadRequest,
        Organization $organization,
        AssetType $assetType,
        float $start
    ): SwitchUploadResponse {
        ($this->organizationFoldersErrorHandler)($switchUploadRequest, $organization);

        $actions[] = sprintf('AssetFolderContainer path: %s', $assetFolderContainer->getPath());
        $actions[] = sprintf('Existing asset path: %s', $existingAsset->getPath());

        $filename = $switchUploadRequest->filename;

        $latestAssetVersionFolder = Asset::getById($this->assetRepository->fetchDeepestChildId((int) $assetFolderContainer->getId()));
        $newVersionCount = (int) $latestAssetVersionFolder?->getFilename() + 1;

        $newAssetVersionFolder = new Asset\Folder();
        $newAssetVersionFolder->setParent($assetFolderContainer);
        $newAssetVersionFolder->setFilename((string) $newVersionCount);
        $newAssetVersionFolder->setPath((string) $latestAssetVersionFolder?->getPath());
        $newAssetVersionFolder->save();

        $actions[] = sprintf('Latest asset version folder id: %s, path: %s and version count: %s', $latestAssetVersionFolder?->getId(), $latestAssetVersionFolder?->getPath(), $newVersionCount);

        $asset = ($this->buildFileAsset)($uploadedFile, $filename, $newAssetVersionFolder);

        if (!($asset instanceof Asset)) {
            try {
                $asset?->delete(); /** @phpstan-ignore-line */
                $newAssetVersionFolder->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('%s is not an Asset. File might be broken.', $asset);

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

        $actions[] = sprintf('Asset: %s already exist and updated as version %s new asset id: %s.', $existingAsset->getId(), $newVersionCount, $asset->getId());

        $assetResourceMetadataFieldCollection = ($this->buildAssetResourceMetadata)($switchUploadRequest);

        $actions[] = sprintf('AssetResourceMetadataFieldCollection is created for asset with ID: %s', $asset->getId());

        $assetResourceFolderName = $switchUploadRequest->customAssetFolder;

        $rootAssetResourceFolder = $organization->getObjectFolder() . "/$assetResourceFolderName/";

        $actions[] = sprintf('RootAssetResourceFolder %s', $rootAssetResourceFolder);

        $assetResourceContainer = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', $filename)
            ->addConditionParam('o_path = ?', $rootAssetResourceFolder)
            ->addConditionParam('Name = ?', $filename)
            ->addConditionParam('o_published = ?', true)
            ->addConditionParam('AssetVersion = ?', 0)
            ->current();

        if (!($assetResourceContainer instanceof AssetResource)) {
            try {
                $asset->delete();
                $newAssetVersionFolder->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('%s is not an AssetResource. Asset and AssetResource might not be in sync. Rolled back to previous state.', $assetResourceContainer);

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

        $tags = ($this->buildTags)($switchUploadRequest, $organization, $actions);

        $assetResourceContainer->setTags($tags);
        $assetResourceContainer->save();

        $latestAssetResourceVersion = AssetResource::getById($this->assetResourceRepository->fetchDeepestChildId((int) $assetResourceContainer->getId()));
        $newAssetResourceVersionCount = (int) $latestAssetResourceVersion?->getAssetVersion() + 1;

        if (!($latestAssetResourceVersion instanceof AssetResource)) {
            try {
                $asset->delete();
                $newAssetVersionFolder->delete();
                $latestAssetResourceVersion?->delete(); /** @phpstan-ignore-line */
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('LatestAssetResourceVersion %s does not exist.', $latestAssetResourceVersion);

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

        $latestAssetResourceVersion->save();

        $newAssetResourceLatestVersion = AssetResource::create();
        $newAssetResourceLatestVersion->setPublished(true);
        $newAssetResourceLatestVersion->setPath($latestAssetResourceVersion->getPath().'/');
        $newAssetResourceLatestVersion->setName($filename);
        $newAssetResourceLatestVersion->setParentId((int) $assetResourceContainer->getId());
        $newAssetResourceLatestVersion->setAsset($asset);
        $newAssetResourceLatestVersion->setAssetType($assetType);
        $newAssetResourceLatestVersion->setMetadata($assetResourceMetadataFieldCollection);
        $newAssetResourceLatestVersion->setAssetVersion($newAssetResourceVersionCount);
        $newAssetResourceLatestVersion->setKey((string) $newAssetResourceVersionCount);

        $newAssetResourceLatestVersion->save();

        $actions[] = sprintf('New AssetResourceLatestVersion with ID %d is created in %s', $newAssetResourceLatestVersion->getId(), $newAssetResourceLatestVersion->getPath());

        if (!($newAssetResourceLatestVersion instanceof AssetResource)) {
            try {
                $asset->delete();
                $newAssetVersionFolder->delete();
                $newAssetResourceLatestVersion->delete();
            } catch (\Exception $exception) {
                throw new \Exception(message: $exception->getMessage());
            }

            $message = sprintf('AssetResourceLatestVersion %s does not exist.', $newAssetResourceLatestVersion);

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

        $existingAssetResources = $organization->getAssetResources();

        $recentAssetResources = array_values(array_unique([...$existingAssetResources, $newAssetResourceLatestVersion, $assetResourceContainer]));

        /** @var array<int, AssetResource> $assetResources */
        $assetResources = array_unique($recentAssetResources);

        $organization->setAssetResources($assetResources);

        $organization->save();

        $parentAssetResource = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $filename)
            ->addConditionParam('o_path = ?', $organization->getObjectFolder().'/'.AssetResourceOrganizationFolderNames::Assets->readable().'/')
            ->current();

        if (!($parentAssetResource instanceof AssetResource)) {
            throw new \Exception(message: 'No AssetResource container folder i.e. /Customers/org-name/Assets/');
        }

        ($this->buildProductFromPayload)($switchUploadRequest, [$parentAssetResource], $organization, $actions);
        ($this->buildProjectFromPayload)($switchUploadRequest, [$parentAssetResource], $organization, $actions);

        $end = microtime(true);
        $elapsed = $end - $start;
        $memUsage = memory_get_usage();
        $usage = ($memUsage / 1024) / 1024;
        $peakUsage = (memory_get_peak_usage() / 1024) / 1024;

        $this->logger->info(
            message: 'Asset Updated' . implode(separator: ',', array: $actions),
            context: [
                'fileObject'    => 'Asset: ' . $asset->getId() . ': ' . $asset->getPath(),
                'relatedObject' => 'AssetResource: ' .  $newAssetResourceLatestVersion->getId() . ': ' . $newAssetResourceLatestVersion->getPath(),
                'component' => $switchUploadRequest->eventName
            ]
        );

        return new SwitchUploadResponse(
            eventName: $switchUploadRequest->eventName,
            date: date('F j, Y H:i'),
            logLevel: LogLevelNames::SUCCESS->name.sprintf(': Asset %s and AssetResource: %s successfully updated!', $asset->getId(), $newAssetResourceLatestVersion->getId()),
            assetId: (string) $asset->getId(),
            assetResourceId: (string) $newAssetResourceLatestVersion->getId(),
            relatedObjects: [
                'productId' => current($newAssetResourceLatestVersion->getProducts()) instanceof Product ? current($newAssetResourceLatestVersion->getProducts())->getId() : '',
                'projectId' => current($newAssetResourceLatestVersion->getProjects()) instanceof Product ? current($newAssetResourceLatestVersion->getProjects())->getId() : '',
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
