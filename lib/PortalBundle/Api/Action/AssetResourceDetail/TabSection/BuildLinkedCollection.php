<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\TabSection;

use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection\LinkedCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection\LinkedItem;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildLinkedCollection
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
    public function __invoke(PaginationInterface $pagination): LinkedCollection
    {
        $linkedAssetResources = [];

        foreach ($pagination->getItems() as $item) {
            if (!($item instanceof AssetResource)) {
                continue;
            }

            $asset = $item->getAsset();

            $linkedAssetResources[] = new LinkedItem(
                id: (int) $item->getId(),
                thumbnail: (string) $this->assetLibraryExtension->getAssetThumbnailPath($asset, 'portal_asset_library_item'),
                filename: $asset?->getFilename() ?? '-',
                productSku: $this->portalDetailExtension->portalAssetResourceProductSku($item),
                assetTypeName: $item->getAssetType()?->getName() ?? '-',
                projectName: $this->portalDetailExtension->portalAssetResourceProjectName($item),
                linkToItem: ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets.detail', ['id' => $item->getId()]),
                downloadLink: $asset ? $this->assetLibraryExtension->portalAssetPath($asset) : '-'
            );
        }

        return new LinkedCollection(
            totalCount: count($linkedAssetResources),
            items: $linkedAssetResources
        );
    }
}
