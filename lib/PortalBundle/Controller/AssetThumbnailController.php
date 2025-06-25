<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use Froq\PortalBundle\Security\AssetPreviewHasher;
use League\Flysystem\FilesystemOperator;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class AssetThumbnailController extends AbstractController
{
    public function __construct(
        private readonly ApplicationLogger $logger,
        private readonly FilesystemOperator $pimcoreAssetStorage,
    ) {
    }

    /**
     * @param AssetPreviewHasher $hasher
     * @param string $thumbnailName
     * @param int $assetID
     * @param string $hash
     *
     * @return Response
     *
     * @throws \Exception
     */
    #[Route('/asset-thumbnail/{thumbnailName}/{assetID}/{hash}', name: 'froq_portal.asset_thumbnail.asset_hashed', methods: ['GET'])]
    #[Cache(maxage: 31536000, smaxage: 31536000, public: true)]
    public function assetThumbnailHashedAction(AssetPreviewHasher $hasher, string $thumbnailName, int $assetID, string $hash): Response
    {
        if (!$hasher->verify($assetID, $hash)) {
            return $this->logAndThrowAccessDenied($assetID, 'Invalid hash');
        }

        $asset = Asset::getById($assetID);

        if (!($asset instanceof Asset)) {
            return $this->logAndThrowNotFound($assetID, 'Asset not found');
        }

        $thumbnailConfig = Asset\Image\Thumbnail\Config::getByName($thumbnailName);

        if (!$thumbnailConfig) {
            return $this->logAndThrowNotFound($assetID, 'Thumbnail config not found');
        }

        $stream = match (true) {
            $asset instanceof Asset\Image => (function () use ($asset, $thumbnailName) {
                if ($this->pimcoreAssetStorage->fileExists($asset->getFullPath())) {
                    return $asset->getThumbnail($thumbnailName, false)->getStream();
                }

                return $asset->getThumbnail($thumbnailName)->getStream();
            })(),
            $asset instanceof Asset\Document => (function () use ($asset, $thumbnailName) {
                if ($this->pimcoreAssetStorage->fileExists($asset->getFullPath())) {
                    return $asset->getImageThumbnail($thumbnailName)->getStream();
                }

                return $asset->getImageThumbnail($thumbnailName, 1, true)->getStream();
            })(),

            default => $this->logAndThrowNotFound($assetID, 'Unsupported Asset type'),
        };

        if (!is_resource($stream)) {
            return $this->logAndThrowNotFound($assetID, 'Thumbnail not found');
        }

        $response = new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $asset->getMimeType(),
        ]);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Cache-Control', $response->headers->get('Cache-Control') . ', immutable');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    private function logAndThrowNotFound(int $assetID, string $message): Response
    {
        $this->logger->warning(sprintf('Asset id: %d: %s', $assetID, $message), [
            'component' => 'can_not_auto_generate_thumbnail',
        ]);

        throw $this->createNotFoundException($message);
    }

    private function logAndThrowAccessDenied(int $assetID, string $message): Response
    {
        $this->logger->warning(sprintf('Asset id: %d: %s', $assetID, $message), [
            'component' => 'can_not_auto_generate_thumbnail',
        ]);

        throw $this->createAccessDeniedException($message);
    }
}
