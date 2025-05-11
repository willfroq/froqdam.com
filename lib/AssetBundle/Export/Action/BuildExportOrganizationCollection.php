<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Action;

use Froq\AssetBundle\Export\DataTransferObject\OrganizationExportCollection;
use Froq\AssetBundle\Export\DataTransferObject\OrganizationExportItem;
use Pimcore\Model\DataObject\Organization;

final class BuildExportOrganizationCollection
{
    /**
     * @throws \Exception
     */
    public function __invoke(int $offset, int $limit): OrganizationExportCollection
    {
        $organizations = Organization::getList()
            ->setOffset($offset)
            ->setLimit($limit)
            ->setOrderKey('Name')
            ->setOrder('asc');

        $organizationExportItems = [];

        foreach ($organizations as $organization) {
            if (!($organization instanceof Organization)) {
                continue;
            }

            $organizationExportItems[] = new OrganizationExportItem(
                id: (int) $organization->getId(),
                code: (int) $organization->getCode(),
                mainContactId: $organization->getMainContact()?->getId(),
                name: (string) $organization->getName(),
                key: (string) $organization->getKey(),
                path: (string) $organization->getPath(),
                objectFolder: (string) $organization->getObjectFolder(),
                assetFolder: (string) $organization->getAssetFolder()
            );
        }

        return new OrganizationExportCollection(
            offset: $offset,
            limit: $limit,
            totalCount: $organizations->count(),
            organizationExportItems: $organizationExportItems
        );
    }
}
