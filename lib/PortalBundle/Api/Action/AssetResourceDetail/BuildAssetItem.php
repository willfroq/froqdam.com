<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail;

use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetItem;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\AssetPreviewExtension;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

final class BuildAssetItem
{
    public function __construct(
        private readonly AssetPreviewExtension $assetPreviewExtension,
        private readonly AssetLibraryExtension $assetLibraryExtension,
    ) {
    }

    public function __invoke(AssetResource $assetResource): AssetItem
    {
        $assetExtension = '';
        $assetDocumentUrl = '';
        $portalAssetPath = '';
        $assetImageUrl = '';
        $assetTextUrl = '';
        $asset = $assetResource->getAsset();

        if ($asset instanceof Asset\Document) {
            $assetDocument = $asset;

            $assetDocumentUrl = $this->assetPreviewExtension->getDocumentPreviewURL($assetDocument);

            $assetExtension = $this->assetPreviewExtension->getAssetExtension($assetDocument);

            $portalAssetPath = $this->assetLibraryExtension->portalAssetPath($assetDocument);
        }

        if ($asset instanceof Asset\Image) {
            $assetImage = $asset;

            $assetImageUrl = $this->assetPreviewExtension->getImagePreviewURL($assetImage, 'portal_asset_detail_preview');

            $assetExtension = $this->assetPreviewExtension->getAssetExtension($assetImage);

            $portalAssetPath = $this->assetLibraryExtension->portalAssetPath($assetImage);
        }

        if ($asset instanceof Asset\Text) {
            $assetText = $asset;

            $assetTextUrl = $this->assetPreviewExtension->getTextPreviewContent($assetText);

            $assetExtension = $this->assetPreviewExtension->getAssetExtension($assetText);

            $portalAssetPath = $this->assetLibraryExtension->portalAssetPath($assetText);
        }

        return new AssetItem(
            id: (int) $assetResource->getAsset()?->getId(),
            assetType: (string) $assetResource->getAsset()?->getType(),
            assetDocumentUrl: (string) $assetDocumentUrl,
            assetImageUrl: (string) $assetImageUrl,
            assetTextUrl: (string) $assetTextUrl,
            assetExtension: $assetExtension,
            portalAssetPath: $portalAssetPath,
            assetFilename: (string) $assetResource->getAsset()?->getFilename(),
        );
    }
}
