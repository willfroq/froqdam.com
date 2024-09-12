<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action\Response;

use Carbon\Carbon;
use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\DataTransferObject\Request\DownloadLinksRequest;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetBasket;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\Uid\Uuid;

final class GeneratePublicUrl
{
    public function __construct(private readonly GetBaseUrl $getBaseUrl)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(DownloadLinksRequest $downloadLinksRequest): string
    {
        $assetBasketFolder = (new Listing())
            ->addConditionParam('o_key = ?', 'AssetBaskets')
            ->addConditionParam('o_path = ?', '/')
            ->current();

        if (!($assetBasketFolder instanceof Folder)) {
            $assetBasketFolder = new Folder();
            $assetBasketFolder->setPath('/');
            $assetBasketFolder->setKey('AssetBaskets');
            $assetBasketFolder->setParentId(1);

            $assetBasketFolder->save();
        }

        $uuid = Uuid::v4()->toRfc4122();

        $assetBasket = new AssetBasket();

        $assetBasket->setUUID($uuid);
        $assetBasket->setExpirationDate((Carbon::now())->addDays(3));
        $assetBasket->setUser([$downloadLinksRequest->user]);
        $assetBasket->setParentId((int) $assetBasketFolder->getId());
        $assetBasket->setKey($uuid);
        $assetBasket->setPublicUrl(($this->getBaseUrl)()."/download-page/?uuid=$uuid");
        $assetBasket->setPublished(true);

        $assetResources = [];

        foreach ($downloadLinksRequest->assetResourceIds as $assetResourceId) {
            if (!is_numeric($assetResourceId)) {
                continue;
            }

            $assetResource = AssetResource::getById((int) $assetResourceId);

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            $asset = $assetResource->getAsset();

            if (!($asset instanceof Asset)) {
                continue;
            }

            $assetResources[] = $assetResource;
        }

        $assetBasket->setAssetResources($assetResources);

        $assetBasket->save();

        return (string) $assetBasket->getPublicUrl();
    }
}
