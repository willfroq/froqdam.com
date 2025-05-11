<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig;

use Froq\AssetBundle\Converter\RtfToHtmlConverter;
use Froq\PortalBundle\Action\GetS3Client;
use Froq\PortalBundle\Action\GetS3PrefixName;
use Froq\PortalBundle\Security\AssetPreviewHasher;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetPreviewExtension extends AbstractExtension
{
    public function __construct(protected RouterInterface $router,
        protected RtfToHtmlConverter $rtfToHtmlConverter,
        protected AssetPreviewHasher $assetPreviewHasher,
        protected ApplicationLogger $logger,
        private readonly GetS3Client $getS3Client,
        private readonly GetS3PrefixName $getS3PrefixName,
        private readonly string $s3BucketNameAssets
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_asset_document_preview_url', [$this, 'getDocumentPreviewURL']),
            new TwigFunction('get_asset_image_preview_url', [$this, 'getImagePreviewURL']),
            new TwigFunction('get_asset_text_preview_content', [$this, 'getTextPreviewContent']),
            new TwigFunction('get_asset_extension', [$this, 'getAssetExtension']),
            new TwigFunction('get_asset_thumbnail_hashed_url', [$this, 'getAssetThumbnailHashedURL']),
            new TwigFunction('get_public_thumbnail_link', [$this, 'getPublicThumbnailLink']),
            new TwigFunction('get_asset_by_id', [$this, 'getAssetById']),
        ];
    }

    public function getDocumentPreviewURL(Asset\Document $asset): ?string
    {
        try {
            if ($asset->getMimeType() === 'application/pdf') {
                return $asset->getFullPath();
            } else {
                return $this->router->generate('froq.asset.asset_preview.document', ['id' => $asset->getId()]);
            }
        } catch (\Exception $ex) {
            $this->logger->warning($ex->getMessage());
        }

        return null;
    }

    public function getImagePreviewURL(Asset\Image $asset, string $thumbnailName = null, bool $deferred = true): null |string | Asset\Image\Thumbnail
    {
        try {
            if ($asset->getMimeType() === 'image/gif') {
                return $asset->getFullPath();
            } else {
                return $asset->getThumbnail($thumbnailName, $deferred);
            }
        } catch (\Exception $ex) {
            $this->logger->warning($ex->getMessage());
        }

        return null;
    }

    public function getTextPreviewContent(Asset\Text $asset): ?string
    {
        try {
            if ($asset->getMimeType() === 'text/rtf') {
                return $this->rtfToHtmlConverter->convert($asset);
            } else {
                return $asset->getData() === false ? null : $asset->getData();
            }
        } catch (\Exception $ex) {
            $this->logger->warning($ex->getMessage());
        }

        return null;
    }

    /**
     * @return array<int, string|null>|string
     */
    public function getAssetExtension(Asset $asset): array|string
    {
        return pathinfo((string) $asset->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * @param ?Asset $asset
     * @param string $thumbnailName
     *
     * @return string
     */
    public function getAssetThumbnailHashedURL(?Asset $asset, string $thumbnailName): string
    {
        if (!($asset instanceof Asset)) {
            return '';
        }

        return $this->router->generate('froq_portal.asset_thumbnail.asset_hashed', [
            'thumbnailName' => $thumbnailName,
            'assetID' => $asset->getId(),
            'hash' => $this->assetPreviewHasher->hash((int) $asset->getId()),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getPublicThumbnailLink(Asset $asset, string $thumbnailName): string
    {
        $thumbnailConfig = Asset\Image\Thumbnail\Config::getByName($thumbnailName);

        if (!$thumbnailConfig) {
            throw new \Exception('ThumbnailConfig not found');
        }

        if ($asset instanceof Asset\Image) {
            $thumbnail = $asset->getThumbnail($thumbnailName, false);
        } elseif ($asset instanceof Asset\Document) {
            $thumbnail = $asset->getImageThumbnail($thumbnailName);
        } else {
            throw new \Exception('Asset not found');
        }

        $stream = $thumbnail->getStream();

        if (!is_resource($stream)) {
            throw new \Exception('Asset not found');
        }
        $s3Client = ($this->getS3Client)();

        $prefix = ($this->getS3PrefixName)();

        return (string) $s3Client->createPresignedRequest(
            $s3Client->getCommand('GetObject', [
                'Bucket' => $this->s3BucketNameAssets,
                'Key'    => "$prefix{$asset->getRealPath()}{$asset->getFilename()}"
            ]),
            '+20 minutes'
        )->getUri();
    }

    public function getAssetById(int $id): ?Asset
    {
        return Asset::getById($id);
    }
}
