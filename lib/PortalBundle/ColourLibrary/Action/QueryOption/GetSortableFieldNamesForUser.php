<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action\QueryOption;

use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetSortableFieldNamesForUser
{
    /** @return  array<int, string> */
    public function __invoke(#[CurrentUser] User $user): array
    {
        // TODO: This has to be dynamic later. Admin should be able to configure which field a user can sort, query, aggregate, search, filter etc.
        return [
            'name',
            'created_at_timestamp',
        ];
    }
}
