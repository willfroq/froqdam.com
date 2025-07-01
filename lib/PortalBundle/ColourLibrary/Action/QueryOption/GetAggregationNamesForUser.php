<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action\QueryOption;

use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetAggregationNamesForUser
{
    /**
     * @param string $indexName
     * @param User $user
     *
     * @return array<int, string>
     */
    public function __invoke(string $indexName, #[CurrentUser] User $user): array
    {
        // TODO: This has to be dynamic later. Admin should be able to configure which field a user can sort, query, aggregate, search, filter etc.
        return [
            'organizations',
            'brands',
            'markets',
        ];
    }
}
