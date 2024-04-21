<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class TagRepository
{
    public function isTagExists(Organization $organization, string $code): bool
    {
        $rootTagFolder = $organization->getObjectFolder() . '/';

        $tagName = AssetResourceOrganizationFolderNames::Tags->name;

        $parentTagFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $tagName)
            ->addConditionParam('o_path = ?', $rootTagFolder)
            ->current();

        if (!($parentTagFolder instanceof DataObject)) {
            return false;
        }

        $tag = (new Tag\Listing())
            ->addConditionParam('o_key = ?', $code)
            ->addConditionParam('o_path = ?', $rootTagFolder . "$tagName/")
            ->current();

        if ($tag instanceof Tag) {
            return true;
        }

        return false;
    }
}
