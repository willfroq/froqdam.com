<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\PortalBundle\Repository\AssetRepository;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UpdateAsset
{
    public function __construct(
        private readonly BuildFileAsset $buildFileAsset,
        private readonly BuildAssetResourceMetadata $buildAssetResourceMetadata,
        private readonly AssetRepository $assetRepository,
        private readonly AssetResourceRepository $assetResourceRepository,
        private readonly BuildProductFromPayload $buildProductFromPayload,
        private readonly BuildProjectFromPayload $buildProjectFromPayload,
        private readonly BuildPrinterFromPayload $buildPrinterFromPayload,
        private readonly BuildSupplierFromPayload $buildSupplierFromPayload,
        private readonly BuildTags $buildTags,
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
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
            $message = sprintf('%s is not an Asset. File might be broken.', $asset);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
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
            $message = sprintf('%s is not an AssetResource. Asset and AssetResource might not be in sync.', $assetResourceContainer);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $latestAssetResourceVersion = AssetResource::getById($this->assetResourceRepository->fetchDeepestChildId((int) $assetResourceContainer->getId()));
        $newAssetResourceVersionCount = (int) $latestAssetResourceVersion?->getAssetVersion() + 1;

        if (!($latestAssetResourceVersion instanceof AssetResource)) {
            $message = sprintf('LatestAssetResourceVersion %s does not exist.', $latestAssetResourceVersion);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $assetResourceLatestVersion = AssetResource::create();
        $assetResourceLatestVersion->setPublished(true);
        $assetResourceLatestVersion->setPath($latestAssetResourceVersion->getPath().'/');
        $assetResourceLatestVersion->setName($filename);
        $assetResourceLatestVersion->setParentId((int) $assetResourceContainer->getId());
        $assetResourceLatestVersion->setAsset($asset);
        $assetResourceLatestVersion->setAssetType($assetType);
        $assetResourceLatestVersion->setAssetVersion($newAssetResourceVersionCount);
        $assetResourceLatestVersion->setKey((string) $newAssetResourceVersionCount);
        $assetResourceLatestVersion->setMetadata($assetResourceMetadataFieldCollection);

        ($this->buildTags)($switchUploadRequest, $assetResourceLatestVersion, $organization, $actions);

        $assetResourceLatestVersion->save();

        $actions[] = sprintf('AssetResourceLatestVersion with ID %d is created in %s', $assetResourceLatestVersion->getId(), $assetResourceLatestVersion->getPath());

        if (!($assetResourceLatestVersion instanceof AssetResource)) {
            $message = sprintf('AssetResourceLatestVersion %s does not exist.', $assetResourceLatestVersion);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $existingAssetResources = $organization->getAssetResources();

        $assetResources = array_unique([...$existingAssetResources, $assetResourceContainer, $latestAssetResourceVersion, $assetResourceLatestVersion]);

        $organization->setAssetResources($assetResources);

        $organization->save();

        ($this->buildProductFromPayload)($switchUploadRequest, $assetResourceLatestVersion, $organization, $actions);
        ($this->buildProjectFromPayload)($switchUploadRequest, $assetResourceLatestVersion, $organization, $actions);
        ($this->buildPrinterFromPayload)($switchUploadRequest, $organization, $actions);
        ($this->buildSupplierFromPayload)($switchUploadRequest, $organization, $actions);

        $end = microtime(true);
        $elapsed = $end - $start;
        $memUsage = memory_get_usage();
        $usage = ($memUsage / 1024) / 1024;
        $peakUsage = (memory_get_peak_usage() / 1024) / 1024;

        $this->logger->info(message: 'Asset Updated' . implode(separator: ',', array: $actions), context: [
            'fileObject'    => 'Asset: ' . $asset->getId() . ': ' . $asset->getPath(),
            'relatedObject' => 'AssetResource: ' .  $assetResourceLatestVersion->getId() . ': ' . $assetResourceLatestVersion->getPath(),
            'component' => $switchUploadRequest->eventName
        ]);

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
