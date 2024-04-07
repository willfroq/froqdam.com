<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\AssetLibrary;

use Froq\PortalBundle\Contract\AssetResourceRepositoryInterface;
use Froq\PortalBundle\Manager\AssetResource\AssetResourceLinkedManager;
use Froq\PortalBundle\Manager\AssetResource\AssetResourceRelatedManager;
use Froq\PortalBundle\Manager\AssetResource\AssetResourceVersionManager;
use Froq\PortalBundle\Repository\AssetTypeRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/detail', name: 'froq_portal.asset_library.')]
class DetailController extends AbstractController
{
    public function __construct(private readonly AssetResourceRepositoryInterface $assetResourceRepository, private readonly AssetTypeRepository $assetTypeRepo)
    {
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detailAction(int $id): Response
    {
        return $this->render('@FroqPortalBundle/asset-library/detail.html.twig', [
            'item' => $this->assetResourceRepository->getAssetResourceByIdWithChecks($id),
            'user' => $this->getUser()
        ]);
    }

    #[Route('/load-versions-tab/{id}', name: 'detail.load_versions_tab', methods: ['GET'])]
    public function loadVersionsTabAction(int $id): Response
    {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $url = $this->generateUrl('froq_portal.asset_library.detail.load_versions_tab_list_items',
            ['id' => $assetResource?->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $response = [
            'html' => $this->renderView('@FroqPortalBundle/partials/load-versions-tab.html.twig', compact('url'))
        ];

        return $this->json($response);
    }

    #[Route('/load-versions-tab-list-items/{id}', name: 'detail.load_versions_tab_list_items', methods: ['GET'])]
    public function loadVersionsTabListItemsAction(int $id, AssetResourceVersionManager $versionManager): Response
    {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $pagination = null;

        if ($assetResource instanceof AssetResource) {
            $pagination = $versionManager->getPaginatedDistinctVersions($assetResource);
        }

        $paginationData = $pagination->getPaginationData(); /** @phpstan-ignore-line */
        $response = [
            'html' => $this->renderView('@FroqPortalBundle/partials/load-versions-tab-list-items.html.twig', compact('pagination')),
            'pages' => $paginationData['pageCount'] ?? 1,
            'next_page' => $paginationData['next'] ?? false,
        ];

        return $this->json($response);
    }

    #[Route('/load-related-tab/{id}', name: 'detail.load_related_tab', methods: ['GET'])]
    public function loadRelatedTabAction(int $id): Response
    {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $assetResourceId = null;

        if ($assetResource instanceof AssetResource) {
            $assetResourceId = $assetResource->getId();
        }

        $url = $this->generateUrl('froq_portal.asset_library.detail.load_related_tab_list_items',
            ['id' => $assetResourceId], UrlGeneratorInterface::ABSOLUTE_URL);

        $assetTypeList = $this->assetTypeRepo->findAssetTypesForUser($this->getUser()); /** @phpstan-ignore-line */
        $response = [
                   'html' => $this->renderView('@FroqPortalBundle/partials/load-related-tab.html.twig', compact('url', 'assetTypeList'))
               ];

        return $this->json($response);
    }

    #[Route('/load-related-tab-list-items/{id}', name: 'detail.load_related_tab_list_items', methods: ['GET'])]
    public function loadRelatedTabListItemsAction(int $id, AssetResourceRelatedManager $relatedManager): Response
    {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $pagination = null;

        if ($assetResource instanceof AssetResource) {
            /** @var SlidingPagination $pagination */
            $pagination = $relatedManager->getRelatedHighestVersions($assetResource);
        }

        $paginationData = $pagination?->getPaginationData();

        $response = [
            'html' => $this->renderView('@FroqPortalBundle/partials/load-related-tab-list-items.html.twig', compact('pagination')),
            'pages' => $paginationData['pageCount'] ?? 1,
            'next_page' => $paginationData['next'] ?? false,
        ];

        return $this->json($response);
    }

    #[Route('/load-linked-tab/{id}', name: 'detail.load_linked_tab', methods: ['GET'])]
    public function loadLinkedTabAction(int $id): Response
    {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $assetResourceId = null;

        if ($assetResource instanceof AssetResource) {
            $assetResourceId = $assetResource->getId();
        }

        $url = $this->generateUrl('froq_portal.asset_library.detail.load_linked_tab_list_items',
            ['id' => $assetResourceId], UrlGeneratorInterface::ABSOLUTE_URL);

        $assetTypeList = $this->assetTypeRepo->findAssetTypesForUser($this->getUser()); /** @phpstan-ignore-line */
        $response = [
            'html' => $this->renderView('@FroqPortalBundle/partials/load-linked-tab.html.twig', compact('url', 'assetTypeList'))
        ];

        return $this->json($response);
    }

    #[Route('/load-linked-tab-list-items/{id}', name: 'detail.load_linked_tab_list_items', methods: ['GET'])]
    public function loadLinkedTabListItemsAction(int $id, AssetResourceLinkedManager $relatedManager): Response
    {
        $assetResource = $this->assetResourceRepository->getAssetResourceByIdWithChecks($id);

        $pagination = null;

        if ($assetResource instanceof AssetResource) {
            $pagination = $relatedManager->getLinkedHighestVersions($assetResource);
        }

        $paginationData = $pagination?->getPaginationData(); /** @phpstan-ignore-line */
        $response = [
            'html' => $this->renderView('@FroqPortalBundle/partials/load-linked-tab-list-items.html.twig', compact('pagination')),
            'pages' => $paginationData['pageCount'] ?? 1,
            'next_page' => $paginationData['next'] ?? false,
        ];

        return $this->json($response);
    }
}
