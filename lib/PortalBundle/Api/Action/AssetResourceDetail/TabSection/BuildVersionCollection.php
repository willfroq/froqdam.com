<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\TabSection;

use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection\VersionCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection\VersionItem;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildVersionCollection
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly PortalDetailExtension $portalDetailExtension,
        private readonly GetBaseUrl $getBaseUrl,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {

    }

    /**
     * @param PaginationInterface<mixed> $pagination
     */
    public function __invoke(PaginationInterface $pagination): VersionCollection
    {
        $versions = [];

        foreach ($pagination->getItems() as $item) {
            if (!($item instanceof AssetResource)) {
                continue;
            }

            $asset = $item->getAsset();

            $versions[] = new VersionItem(
                id: (int) $item->getId(),
                thumbnail: (string) $this->assetLibraryExtension->getAssetThumbnailPath($asset, 'portal_asset_library_item'),
                filename: $asset?->getFilename() ?? '-',
                modificationDate: date('Y-m-d', $item->getAsset()?->getModificationDate()),
                version: $this->portalDetailExtension->portalAssetResourceVersion($item),
                linkToItem: ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets.detail', ['id' => $item->getId()]),
                downloadLink: $asset ? $this->assetLibraryExtension->portalAssetPath($asset) : '-'
            );
        }

        return new VersionCollection(
            totalCount: count($versions),
            items: $versions
        );
    }
}
