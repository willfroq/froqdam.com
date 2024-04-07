<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig;

use Froq\AssetBundle\Converter\RtfToHtmlConverter;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetPreviewExtension extends AbstractExtension
{
    public function __construct(protected RouterInterface $router, protected RtfToHtmlConverter $rtfToHtmlConverter, protected ApplicationLogger $logger)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_asset_document_preview_url', [$this, 'getDocumentPreviewURL']),
            new TwigFunction('get_asset_image_preview_url', [$this, 'getImagePreviewURL']),
            new TwigFunction('get_asset_text_preview_content', [$this, 'getTextPreviewContent']),
            new TwigFunction('get_asset_extension', [$this, 'getAssetExtension']),
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
}
