<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Pimcore\Model\DataObject\Organization;

final class OrganizationRepository
{
    public function getByOrganizationCode(string $code): ?Organization
    {
        $organization = (new Organization\Listing())
            ->addConditionParam('Code = ?', $code)
            ->current();

        if (!($organization instanceof Organization)) {
            return null;
        }

        return $organization;
    }
}
