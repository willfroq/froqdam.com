<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Exception;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

final class DeleteAssetAndAssetResourceVersionOne
{
    public function __construct(private readonly ApplicationLogger $applicationLogger, private readonly AssetResourceRepository $assetResourceRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function __invoke(AssetResource $assetResource): void
    {
        $assetResourceContainer = AssetResource::getById((int) $assetResource->getParentId());

        if (!($assetResourceContainer instanceof AssetResource)) {
            return;
        }

        if ($assetResourceContainer->getChildAmount() !== 1 && $assetResource->getKey() !== '1') {
            return;
        }

        sleep(2);

        $asset = AssetResource::getById($this->assetResourceRepository->fetchDeepestChildId((int) $assetResourceContainer->getId()))?->getAsset();
        $versionOneFolder = Asset::getById((int) $asset?->getParentId());
        $containerFolder = Asset::getById((int) $versionOneFolder?->getParentId());

        try {
            if ($asset instanceof Asset) {
                $asset->delete();

                $this->applicationLogger->info(message: sprintf('Deleted Asset: %s', $asset->getId()));
            }

            if ($versionOneFolder instanceof Asset\Folder) {
                $versionOneFolder->delete();

                $this->applicationLogger->info(message: sprintf('Deleted Asset: %s', $versionOneFolder->getId()));
            }

            if ($containerFolder instanceof Asset\Folder) {
                $containerFolder->delete();

                $this->applicationLogger->info(message: sprintf('Deleted Asset: %s', $containerFolder->getId()));
            }

            foreach ($assetResource->getChildren() ?? [] as $assetResourceChild) {
                if (!($assetResourceChild instanceof AssetResource)) {
                    continue;
                }

                $assetResourceChild->delete();

                $this->applicationLogger->info(message: sprintf('Deleted AssetResource: %s', $assetResourceChild->getId()));
            }

            $assetResource->delete();

            $this->applicationLogger->info(message: sprintf('Deleted AssetResource: %s', $assetResource->getId()));

            $assetResourceContainer->delete();

            $this->applicationLogger->info(message: sprintf('Deleted AssetResource: %s', $assetResourceContainer->getId()));
        } catch (\Exception $e) {
            $this->applicationLogger->error(message: $e->getMessage());

            throw new \Exception($e->getMessage());
        }
    }
}
