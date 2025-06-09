<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Sort;

use Elastica\Query;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\Factory\GetItemNamesFactory;
use Froq\PortalBundle\Opensearch\Enum\SortNames;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildSortQuery
{
    public function __construct(
        private readonly GetSortMappingForUser $getSortMappingForUser,
        private readonly GetItemNamesFactory $getItemNamesFactory
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Query $query, SearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        $sortBy = !empty($searchRequest->sortBy) ? $searchRequest->sortBy : SortNames::CreationDate->readable();
        $sortDirection = !empty($searchRequest->sortDirection) ? $searchRequest->sortDirection : SortNames::Desc->readable();

        $sortMappingForUser = ($this->getSortMappingForUser)($user, $searchRequest->searchIndex);

        $sortableFields = array_values(
            array_intersect(
                array_keys($sortMappingForUser),
                ($this->getItemNamesFactory->create($searchRequest->searchIndex))()
            )
        );

        if (!empty($sortMappingForUser[$sortBy]) && in_array($sortBy, $sortableFields, true)) {
            $query->setSort([$sortBy => ['order' => $sortDirection]]);
        }
    }
}
