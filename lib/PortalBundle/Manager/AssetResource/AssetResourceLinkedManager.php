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

class AssetResourceLinkedManager
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
    public function getLinkedHighestVersions(AssetResource $assetResource): PaginationInterface
    {
        $linkedIds = $this->getLinkedHighestVersionsIds($assetResource);

        if (empty($linkedIds)) {
            return $this->paginator->paginate([]);
        }

        $list = new AssetResource\Listing();
        $list->setCondition('object_AssetResource.o_id != :id AND object_AssetResource.o_id IN (:linkedIds)', [
            'id'        => $assetResource->getId(),
            'linkedIds' => $linkedIds
        ]);

        $list = $this->filtersManager->apply($list);

        $page = (int) $this->request?->get('page', 1);
        $limit = (int) $this->request?->get('size', 4);

        return $this->paginator->paginate(
            $list,
            $page === 0 ? 1 : $page,
            $limit === 0 ? 4 : $limit,
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function getLinkedHighestVersionsIds(AssetResource $assetResource): array
    {
        $assetResource = AssetResourceHierarchyHelper::getSourceAssetResource($assetResource);

        $linkedIds = $assetResource->getDao()->getRelationIds('Contains');

        $usedInAssetResourceListing = new AssetResource\Listing();
        $usedInAssetResourceListing->filterByContains($assetResource->getId());

        foreach ($usedInAssetResourceListing as $assetResource) {
            if (!$assetResource) {
                continue;
            }

            $linkedIds[] = $assetResource->getId();
        }

        $highestVersionsIds = [];
        foreach (array_unique($linkedIds) as $assetId) {
            $assetResource = AssetResource::getById($assetId);

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            $highestVersionsIds[] = AssetResourceHierarchyHelper::getLatestVersion($assetResource)->getId();
        }

        return array_unique($highestVersionsIds);
    }
}
