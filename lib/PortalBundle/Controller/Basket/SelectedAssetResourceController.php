<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\Basket;

use Froq\PortalBundle\DataTransferObject\Request\SelectedAssetResource;
use Froq\PortalBundle\Twig\AssetPreviewExtension;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/selected-asset-resource', name: 'froq_basket.selected_asset_resource', methods: [Request::METHOD_POST])]
final class SelectedAssetResourceController extends AbstractController
{
    public function __invoke(Request $request, AssetPreviewExtension $assetPreviewExtension): Response
    {
        $assetResourceIds = (array) json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createNotFoundException();
        }

        $selectedAssetResources = [];

        foreach ($assetResourceIds as $assetResourceId) {
            $assetResource = AssetResource::getById($assetResourceId);

            if (!($assetResource instanceof AssetResource)) {
                throw $this->createNotFoundException(message: 'AssetResource not found');
            }

            $asset = $assetResource->getAsset();

            if (!($asset instanceof Asset)) {
                throw $this->createNotFoundException(message: 'File not found');
            }

            $project = current($assetResource->getProjects());

            $selectedAssetResources[] = new SelectedAssetResource(
                id: (int) $assetResource->getId(),
                filename: (string) $assetResource->getAsset()?->getFilename(),
                assetType: (string) $assetResource->getAssetType()?->getName(),
                projectName: $project instanceof Project ? (string) $project->getName() : '',
                thumbnail: $assetPreviewExtension->getAssetThumbnailHashedURL($asset, 'portal_asset_library_item_grid'),
            );
        }

        return $this->json($selectedAssetResources);
    }
}
