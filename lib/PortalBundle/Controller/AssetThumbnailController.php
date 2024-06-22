<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use Froq\PortalBundle\Security\AssetPreviewHasher;
use Pimcore\Model\Asset;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class AssetThumbnailController extends AbstractController
{
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
            throw $this->createAccessDeniedException('Invalid hash');
        }

        $asset = Asset::getById($assetID);
        if (!$asset) {
            return $this->render('@FroqPortal/partials/thumbnail-placeholder.html.twig');
        }

        $thumbnailConfig = Asset\Image\Thumbnail\Config::getByName($thumbnailName);
        if (!$thumbnailConfig) {
            return $this->render('@FroqPortal/partials/thumbnail-placeholder.html.twig');
        }

        if ($asset instanceof Asset\Image) {
            $thumbnail = $asset->getThumbnail($thumbnailName, false);
        } elseif ($asset instanceof Asset\Document) {
            $thumbnail = $asset->getImageThumbnail($thumbnailName);
        } else {
            throw $this->createNotFoundException('Unsupported Asset type');
        }

        $stream = $thumbnail->getStream();
        if (!$stream) {
            return $this->render('@FroqPortal/partials/thumbnail-placeholder.html.twig');
        }

        $response = new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $thumbnail->getMimeType(),
            'Access-Control-Allow-Origin', '*',
        ]);

        if (!str_starts_with(haystack: (string) $response->headers->get('Content-Type'), needle: 'image/')) {
            return $this->render('@FroqPortal/partials/thumbnail-placeholder.html.twig');
        }

        return $response;
    }
}
