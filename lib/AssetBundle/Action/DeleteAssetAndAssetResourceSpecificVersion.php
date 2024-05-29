<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Exception;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

final class DeleteAssetAndAssetResourceSpecificVersion
{
    public function __construct(private readonly ApplicationLogger $applicationLogger)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function __invoke(AssetResource $assetResource): void
    {
        $assetResourceContainer = AssetResource::getById((int) $assetResource->getParentId());

        if (!($assetResourceContainer instanceof AssetResource)) {
            return;
        }

        if ($assetResourceContainer->getChildAmount() < 1) {
            return;
        }

        sleep(2);

        $currentAssetResource = null;

        foreach ($assetResourceContainer->getChildren() ?? [] as $assetResourceChild) {
            if (!($assetResourceChild instanceof AssetResource)) {
                continue;
            }

            if ($assetResourceChild->getKey() !== $assetResource->getKey()) {
                continue;
            }

            $currentAssetResource = $assetResourceChild;
        }

        $asset = $currentAssetResource?->getAsset();

        $versionFolder = Asset::getById((int) $asset?->getParentId());

        try {
            if ($asset instanceof Asset) {
                $asset->delete();

                $this->applicationLogger->info(message: sprintf('Deleted Asset: %s', $asset->getId()));
            }

            if ($versionFolder instanceof Asset\Folder && $versionFolder->getKey() === $assetResource->getKey()) {
                $versionFolder->delete();

                $this->applicationLogger->info(message: sprintf('Deleted Asset: %s', $versionFolder->getId()));
            }

            $currentAssetResource?->delete();

            $this->applicationLogger->info(message: sprintf('Deleted AssetResource: %s', $currentAssetResource?->getId()));
        } catch (\Exception $e) {
            $this->applicationLogger->error(message: $e->getMessage());

            throw new \Exception($e->getMessage());
        }
    }
}
