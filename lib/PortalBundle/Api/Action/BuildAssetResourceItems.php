<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action;

use Froq\PortalBundle\Api\ValueObject\AssetResource\AssetResourceItem;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;

final class BuildAssetResourceItems
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly PortalDetailExtension $portalDetailExtension,
        private readonly GetBaseUrl $getBaseUrl,
    ) {
    }

    /**
     * @param array<int, AssetResource> $assetResources
     *
     * @return array<int, mixed>
     */
    public function __invoke(array $assetResources): array
    {
        $items = [];

        foreach ($assetResources as $assetResource) {
            $latestAssetResource = AssetResourceHierarchyHelper::getLatestVersion($assetResource);

            $items[] = new AssetResourceItem(
                assetResourceId: (int) $latestAssetResource->getId(),
                thumbnailLink: ($this->getBaseUrl)() . $this->assetLibraryExtension->getAssetThumbnailPath($latestAssetResource->getAsset(), 'portal_asset_library_item'),
                filename: $latestAssetResource->getAsset()?->getFilename() ?? '-',
                assetType: $latestAssetResource->getAssetType()?->getName() ?? '-',
                projectName: $this->portalDetailExtension->portalAssetResourceProjectName($latestAssetResource),
                downloadLink: $this->assetLibraryExtension->portalAssetPath($latestAssetResource->getAsset()),
                assetResourceCreationDate: date('Y-m-d', $latestAssetResource->getCreationDate()),
                assetCreationDate: date('Y-m-d', $latestAssetResource->getAsset()?->getCreationDate()),
            );
        }

        return $items;
    }
}
