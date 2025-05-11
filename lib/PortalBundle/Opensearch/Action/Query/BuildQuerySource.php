<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Query;

use Elastica\Query;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\Filter\GetFilterMappingForUser;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildQuerySource
{
    public function __construct(private readonly GetFilterMappingForUser $getFilterMappingForUser)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Query $query, SearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        /** @var array<int, non-empty-string> $filtersForSource */
        $filtersForSource = array_keys(($this->getFilterMappingForUser)($user, $searchRequest->searchIndex));

        $query->setSource($filtersForSource)->setStoredFields([]);
    }
}
