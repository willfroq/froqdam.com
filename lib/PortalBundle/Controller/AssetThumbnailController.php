<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use Froq\PortalBundle\Security\AssetPreviewHasher;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class AssetThumbnailController extends AbstractController
{
    public function __construct(private readonly ApplicationLogger $logger)
    {
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
    #[Cache(maxage: 3600)]
    public function assetThumbnailHashedAction(AssetPreviewHasher $hasher, string $thumbnailName, int $assetID, string $hash): Response
    {
        if (!$hasher->verify($assetID, $hash)) {
            $this->logger->error(sprintf('Asset id: %s can not auto generate a thumbnail in the frontend.', $assetID), ['component' => 'can_not_auto_generate_thumbnail']);

            throw $this->createAccessDeniedException('Invalid hash');
        }

        $asset = Asset::getById($assetID);
        if (!$asset) {
            $this->logger->error(sprintf('Asset id: %s can not auto generate a thumbnail in the frontend.', $assetID), ['component' => 'can_not_auto_generate_thumbnail']);

            throw $this->createNotFoundException('Asset not found');
        }

        $thumbnailConfig = Asset\Image\Thumbnail\Config::getByName($thumbnailName);
        if (!$thumbnailConfig) {
            $this->logger->error(sprintf('Asset id: %s can not auto generate a thumbnail in the frontend.', $assetID), ['component' => 'can_not_auto_generate_thumbnail']);

            throw $this->createNotFoundException('Thumbnail config not found');
        }

        if ($asset instanceof Asset\Image) {
            $thumbnail = $asset->getThumbnail($thumbnailName, false);
        } elseif ($asset instanceof Asset\Document) {
            $thumbnail = $asset->getImageThumbnail($thumbnailName);
        } else {
            $this->logger->error(sprintf('Asset id: %s can not auto generate a thumbnail in the frontend.', $assetID), ['component' => 'can_not_auto_generate_thumbnail']);

            throw $this->createNotFoundException('Unsupported Asset type');
        }

        $stream = $thumbnail->getStream();
        if (!$stream) {
            $this->logger->error(sprintf('Asset id: %s can not auto generate a thumbnail in the frontend.', $assetID), ['component' => 'can_not_auto_generate_thumbnail']);

            throw $this->createNotFoundException('Thumbnail not found');
        }

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $thumbnail->getMimeType(),
            'Access-Control-Allow-Origin', '*',
        ]);
    }
}
