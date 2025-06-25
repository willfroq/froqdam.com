<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Query;

use Elastica\Query;
use Froq\PortalBundle\AssetLibrary\DataTransferObject\SearchRequest as AssetSearchRequest;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest as ColourSearchRequest;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildQuerySource
{
    /**
     * @throws \Exception
     */
    public function __invoke(Query $query, ColourSearchRequest|AssetSearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        /** @var array<'excludes'|'includes'|int, array<int, non-empty-string>|non-empty-string>|non-empty-string|false $querySource */
        $querySource = $searchRequest->querySource;

        $query->setSource($querySource);
    }
}
