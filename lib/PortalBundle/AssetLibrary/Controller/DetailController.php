<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Controller;

use Froq\PortalBundle\Contract\AssetResourceRepositoryInterface;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class DetailController extends AbstractController
{
    #[Route('/detail/{id}', name: 'froq_portal.asset_library.detail', methods: [Request::METHOD_GET])]
    public function __invoke(int $id, #[CurrentUser] User $user, AssetResourceRepositoryInterface $assetResourceRepository): Response
    {
        $assetResource = $assetResourceRepository->getAssetResourceByIdWithChecks($id);

        if (!($assetResource instanceof AssetResource)) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        return $this->render(
            '@FroqPortalBundle/asset-library/detail.html.twig',
            [
                'item' => $assetResource,
                'user' => $user,
            ]
        );
    }
}
