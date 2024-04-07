<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Controller\DetailPage;

use Froq\PortalBundle\Api\Action\AssetResourceDetail\BuildAssetItem;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\BuildAssetResourceDetail;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\BuildSettingsItem;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\TabSection\BuildLinkedCollection;
use Froq\PortalBundle\Api\Action\GetPaginator;
use Froq\PortalBundle\Contract\AssetResourceRepositoryInterface;
use Froq\PortalBundle\Manager\AssetResource\AssetResourceLinkedManager;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/assets/{id}/linked', name: 'froq_portal_api.assets.detail.linked')]
final class LinkedController extends AbstractController
{
    public function __construct(
        private readonly AssetResourceRepositoryInterface $assetResourceRepository,
        private readonly AssetResourceLinkedManager $assetResourceLinkedManager,
    ) {
    }

    public function __invoke(
        Request $request,
        #[CurrentUser] User $currentUser,
        int $id,
        BuildAssetItem $buildAssetItem,
        BuildSettingsItem $buildSettingsItem,
        BuildAssetResourceDetail $buildAssetResourceDetail,
        GetPaginator $getPaginator,
        BuildLinkedCollection $buildLinkedCollection
    ): JsonResponse {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        if (!($assetResource instanceof AssetResource)) {
            return $this->json(data: ['message' => 'Page not found.'], status:  404);
        }

        /** @var SlidingPagination $pagination */
        $pagination = $this->assetResourceLinkedManager->getLinkedHighestVersions($assetResource);

        $paginationData = $pagination->getPaginationData();

        return $this->json(
            [
                'linkedAssetResources' => ($buildLinkedCollection)($pagination),
                'pagination' => [
                    'pages' => $paginationData['pageCount'] ?? 1,
                    'next_page' => $paginationData['next'] ?? false,
                ]
            ]
        );
    }
}
