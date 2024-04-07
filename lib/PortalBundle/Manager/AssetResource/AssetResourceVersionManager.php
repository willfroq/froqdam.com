<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Froq\PortalBundle\Manager\AssetResource\ModelFilter\AssetResourceFiltersManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetResourceVersionManager
{
    private readonly Request | null $request;

    public function __construct(RequestStack $requestStack, private readonly PaginatorInterface $paginator, private readonly AssetResourceFiltersManager $filtersManager)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param AssetResource $assetResource
     *
     * @return PaginationInterface<mixed>
     */
    public function getPaginatedDistinctVersions(AssetResource $assetResource): PaginationInterface
    {
        $list = new AssetResource\Listing();
        $list = $this->filtersManager->apply($list);
        $list->setCondition('object_AssetResource.o_parentId = :parentId', [
            'parentId' => AssetResourceHierarchyHelper::getSourceAssetResource($assetResource)->getId()
        ]);

        $page = (int) $this->request?->get('page', 1);
        $limit = (int) $this->request?->get('size', 4);

        return $this->paginator->paginate(
            $list,
            $page === 0 ? 1 : $page,
            $limit === 0 ? 4 : $limit,
        );
    }
}
