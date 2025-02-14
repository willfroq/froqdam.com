<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetResourceVersionManager
{
    private readonly Request | null $request;

    public function __construct(RequestStack $requestStack, private readonly PaginatorInterface $paginator)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param AssetResource $assetResource
     *
     * @return PaginationInterface<mixed>|null
     */
    public function getPaginatedDistinctVersions(AssetResource $assetResource): ?PaginationInterface
    {
        $parentAssetResource = $assetResource->getParent();

        if (!($parentAssetResource instanceof AssetResource)) {
            return null;
        }

        $page = (int) $this->request?->get('page', 1);
        $limit = (int) $this->request?->get('size', 4);

        $children = array_filter(
            $parentAssetResource->getChildren(),
            fn ($child) => $child !== $assetResource
        );

        return $this->paginator->paginate(
            $children,
            $page === 0 ? 1 : $page,
            $limit === 0 ? 4 : $limit,
        );
    }
}
