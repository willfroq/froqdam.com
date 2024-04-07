<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Action\BuildFileAsset;
use Froq\PortalBundle\Repository\AssetRepository;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadResponse;
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
        string $filename,
        float $start
    ): SwitchUploadResponse {
        $actions[] = sprintf('AssetFolderContainer path: %s', $assetFolderContainer->getPath());
        $actions[] = sprintf('Existing asset path: %s', $existingAsset->getPath());

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
            $actions[] = sprintf('%s is not an Asset. File might be broken.', $asset);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $actions[] = sprintf('Asset: %s already exist and updated as version %s new asset id: %s.', $existingAsset->getId(), $newVersionCount, $asset->getId());

        $assetResourceMetadataFieldCollection = ($this->buildAssetResourceMetadata)($switchUploadRequest, $asset);

        $actions[] = sprintf('AssetResourceMetadataFieldCollection is created for asset with ID: %s', $asset->getId());

        $assetType = AssetType::getByName($switchUploadRequest->assetType)?->current(); /** @phpstan-ignore-line */
        if (!($assetType instanceof AssetType)) {
            $actions[] = sprintf('%s is not an AssetType.', $assetType);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $rootAssetResourceFolder = $organization->getObjectFolder() . '/Assets/';

        $actions[] = sprintf('RootAssetResourceFolder %s', $rootAssetResourceFolder);

        $assetResourceContainer = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', $filename)
            ->addConditionParam('o_path = ?', $rootAssetResourceFolder)
            ->addConditionParam('Name = ?', $filename)
            ->addConditionParam('o_published = ?', true)
            ->addConditionParam('AssetVersion = ?', 0)
            ->current();

        if (!($assetResourceContainer instanceof AssetResource)) {
            $actions[] = sprintf('%s is not an AssetResource. Asset and AssetResource might not be in sync.', $assetResourceContainer);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $latestAssetResourceVersion = AssetResource::getById($this->assetResourceRepository->fetchDeepestChildId((int) $assetResourceContainer->getId()));
        $newAssetResourceVersionCount = (int) $latestAssetResourceVersion?->getAssetVersion() + 1;

        if (!($latestAssetResourceVersion instanceof AssetResource)) {
            $actions[] = sprintf('LatestAssetResourceVersion %s does not exist.', $latestAssetResourceVersion);

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

        $assetResourceLatestVersion->save();

        $actions[] = sprintf('AssetResourceLatestVersion with ID %d is created in %s', $assetResourceLatestVersion->getId(), $assetResourceLatestVersion->getPath());

        if (!($assetResourceLatestVersion instanceof AssetResource)) {
            $actions[] = sprintf('AssetResourceLatestVersion %s does not exist.', $assetResourceLatestVersion);

            return new SwitchUploadResponse(eventName: $switchUploadRequest->eventName, date: date('F j, Y H:i'), actions: $actions, statistics: []);
        }

        $end = microtime(true);
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
