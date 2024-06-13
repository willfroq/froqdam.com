<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class TagRepository
{
    /**
     * @throws \Exception
     */
    public function isTagExists(Organization $organization, string $code): bool
    {
        $parentTagFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Tags->readable())
            ->addConditionParam('o_path = ?', $organization->getObjectFolder() . '/')
            ->current();

        if (!($parentTagFolder instanceof DataObject)) {
            throw new \Exception(message: 'No Tag folder folder i.e. /Customers/org-name/Tags/');
        }

        $tag = (new Tag\Listing())
            ->addConditionParam('o_key = ?', $code)
            ->addConditionParam('o_path = ?', $organization->getObjectFolder().'/'.AssetResourceOrganizationFolderNames::Tags->readable().'/')
            ->current();

        if ($tag instanceof Tag) {
            return true;
        }

        return false;
    }
}
