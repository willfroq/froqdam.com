<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Froq\PortalBundle\Manager\AssetResource\ModelFilter\AssetResourceFiltersManager;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetResourceRelatedManager
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
    public function getRelatedHighestVersions(AssetResource $assetResource): PaginationInterface|SlidingPagination
    {
        $relatedIds = $this->getRelatedHighestVersionsIds($assetResource);

        if (empty($relatedIds)) {
            return $this->paginator->paginate([]);
        }

        $list = new AssetResource\Listing();
        $list->setCondition('object_AssetResource.o_id != :id AND object_AssetResource.o_id IN (:relatedIds)', [
            'id' => $assetResource->getId(),
            'relatedIds' => $relatedIds
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
    private function getRelatedHighestVersionsIds(AssetResource $assetResource): array
    {
        $assetResource = AssetResourceHierarchyHelper::getSourceAssetResource($assetResource);

        $productListing = new Product\Listing();
        $productListing->filterByAssets($assetResource->getId());

        $assetIds = [];

        foreach ($productListing as $product) {
            if (!$product) {
                continue;
            }

            $assetIds = array_merge(
                $assetIds,
                $product->getDao()->getRelationIds('Assets')
            );
        }

        $projectListing = new Project\Listing();
        $projectListing->filterByAssets($assetResource->getId());

        foreach ($projectListing as $project) {
            if (!$project) {
                continue;
            }

            $assetIds = array_merge(
                $assetIds,
                $project->getDao()->getRelationIds('Assets')
            );
        }

        $highestVersionsIds = [];
        foreach (array_unique($assetIds) as $assetId) {
            $assetResource = AssetResource::getById($assetId);

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            $highestVersionsIds[] = AssetResourceHierarchyHelper::getLatestVersion($assetResource)->getId();
        }

        return array_unique($highestVersionsIds);
    }
}
