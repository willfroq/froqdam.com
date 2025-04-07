<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Upload\Builder;

use Exception;
use Froq\AssetBundle\Pimtoday\Controller\Request\FileRequest;
use Froq\AssetBundle\Pimtoday\Controller\Request\FileResponse;
use Froq\AssetBundle\Pimtoday\Enum\ThumbnailTypes;
use Froq\PortalBundle\Twig\AssetPreviewExtension;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;

final class BuildFileResponse
{
    public function __construct(
        private readonly AssetPreviewExtension $assetPreviewExtension,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(FileRequest $fileRequest): FileResponse
    {
        $assetResource = AssetResource::getById((int) $fileRequest->assetResourceId);

        if (!($assetResource instanceof AssetResource)) {
            throw new Exception(message: sprintf('File does not exist: BuildFileResponse.php line: %s', __LINE__));
        }

        $asset = $assetResource->getAsset();

        if (!($asset instanceof Asset)) {
            throw new Exception(message: sprintf('File does not exist: BuildFileResponse.php line: %s', __LINE__));
        }

        $parentAssetResource = $assetResource->getParent();

        if (!($parentAssetResource instanceof AssetResource)) {
            throw new Exception(message: sprintf('Must have a parentAssetResource: BuildFileResponse.php line: %s', __LINE__));
        }

        $relatedProduct = current($parentAssetResource->getProducts());
        $relatedProject = current($parentAssetResource->getProjects());

        $product = $relatedProduct instanceof Product ? $relatedProduct : null;
        $project = $relatedProject instanceof Project ? $relatedProject : null;

        return new FileResponse(
            date: date('F j, Y H:i'),
            pimtodayProjectId: (int) $project?->getPimtodayId(),
            damProjectId: $project?->getId(),
            pimtodaySkuId: (int) $product?->getPimtodayId(),
            damSkuId: $product?->getId(),
            pimtodayDocumentId: (int) $assetResource->getPimtodayId(),
            damAssetResourceId: $assetResource->getId(),
            gridThumbnailLink: $this->assetPreviewExtension->getPublicThumbnailLink($asset, ThumbnailTypes::Grid->value),
            listThumbnailLink: $this->assetPreviewExtension->getPublicThumbnailLink($asset, ThumbnailTypes::List->value),
            imagePreviewLink: $this->assetPreviewExtension->getPublicThumbnailLink($asset, ThumbnailTypes::Preview->value),
        );
    }
}
