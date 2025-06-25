<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Sort;

use Elastica\Query;
use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchRequest as AssetSearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest as ColourSearchRequest;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSortQuery
{
    /**
     * @throws \Exception
     */
    public function __invoke(Query $query, ColourSearchRequest|AssetSearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        $query->setSort([$searchRequest->sortBy => ['order' => $searchRequest->sortDirection]]);
    }
}
