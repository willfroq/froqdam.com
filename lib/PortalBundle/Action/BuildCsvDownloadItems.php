<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

use Froq\PortalBundle\Contract\AssetLibraryExtensionInterface;
use Froq\PortalBundle\Contract\PortalDetailExtensionInterface;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;

final class BuildCsvDownloadItems
{
    public function __construct(
        private readonly AssetLibraryExtensionInterface $assetLibraryExtension,
        private readonly PortalDetailExtensionInterface $portalDetailExtension,
    ) {

    }

    /**
     * @param array<int, AssetResource> $assetResources
     *
     * @return array<int, mixed>
     */
    public function __invoke(?array $assetResources): array
    {
        $items = [
            ['filename', 'assetType', 'projectName', 'downloadLink', 'assetResourceCreationDate', 'assetCreationDate']
        ];

        foreach ($assetResources ?? [] as $assetResource) {
            $latestAssetResource = AssetResourceHierarchyHelper::getLatestVersion($assetResource);

            $items[] = [
                $latestAssetResource->getAsset()?->getFilename() ?? '-',
                $latestAssetResource->getAssetType()?->getName() ?? '-',
                $this->portalDetailExtension->portalAssetResourceProjectName($latestAssetResource),
                $this->assetLibraryExtension->portalAssetPath($latestAssetResource->getAsset()),
                date('Y-m-d', $latestAssetResource->getCreationDate()),
                date('Y-m-d', $latestAssetResource->getAsset()?->getCreationDate()),
            ];
        }

        return $items;
    }
}
