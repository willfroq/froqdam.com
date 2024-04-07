<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Pimcore\Model\DataObject\User;

class UserRepository
{
    /**
     * @return array<int, int|bool|float|string>
     */
    public function getOrganizationIDs(User $user): array
    {
        return array_map(function ($org) {
            return $org->getId() ?? '';
        }, $user->getOrganizations());
    }
}
