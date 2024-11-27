<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\PublicPage;

use Froq\PortalBundle\DataTransferObject\Response\DownloadPageResponse;
use Pimcore\Model\DataObject\AssetBasket;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/download-page', name: 'download_page', methods: [Request::METHOD_GET])]
final class DownloadPublicPageController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $assetBasket = AssetBasket::getByUUID($request->get('uuid'))?->current(); /** @phpstan-ignore-line */
        if (!($assetBasket instanceof AssetBasket)) {
            throw new \Exception(message: 'AssetBasket does not exist.');
        }

        if ($assetBasket->getExpirationDate()?->getTimestamp() < time()) {
            throw new \Exception(message: 'AssetBasket has expired.');
        }

        $user = current($assetBasket->getUser());

        if (!($user instanceof User)) {
            throw new \Exception(message: 'AssetBasket must have a user.');
        }

        return $this->render(
            view: '@FroqPortal/download-page.html.twig',
            parameters: (new DownloadPageResponse(
                assetResources: $assetBasket->getAssetResources(),
                expiryDate: $assetBasket->getExpirationDate()->format('F j, Y H:i'),
            ))->toArray()
        );
    }
}
