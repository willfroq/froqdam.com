<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Controller\DetailPage;

use Froq\PortalBundle\Api\Action\AssetResourceDetail\BuildAssetItem;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\BuildAssetResourceDetail;
use Froq\PortalBundle\Api\Action\AssetResourceDetail\BuildSettingsItem;
use Froq\PortalBundle\Contract\AssetResourceRepositoryInterface;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/assets/{id}', name: 'froq_portal_api.assets.detail')]
final class DetailController extends AbstractController
{
    public function __construct(private readonly AssetResourceRepositoryInterface $assetResourceRepository)
    {
    }

    public function __invoke(
        #[CurrentUser] User $currentUser,
        int $id,
        BuildAssetItem $buildAssetItem,
        BuildSettingsItem $buildSettingsItem,
        BuildAssetResourceDetail $buildAssetResourceDetail,
    ): JsonResponse {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $userSettings = $currentUser->getGroupAssetLibrarySettings();

        if (!($assetResource instanceof AssetResource) || !($userSettings instanceof GroupAssetLibrarySettings)) {
            return $this->json(data: ['message' => 'Page not found.'], status:  404);
        }

        return $this->json(
            ($buildAssetResourceDetail)(
                $assetResource,
                $userSettings,
                ($buildSettingsItem)($assetResource, $currentUser, $userSettings),
                $currentUser
            )
        );
    }
}
