<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\RelatedObject;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;

final class CreateCategoryFolder
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $path): DataObject
    {
        $containerFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', (string) $organization->getKey())
            ->addConditionParam('o_path = ?', '/Customers/')
            ->current();

        if (!($containerFolder instanceof DataObject\Folder)) {
            throw new \Exception(message: sprintf('CreateCategoryFolder: No container folder i.e. /Customers/org-name. Path: %s does not exist!!!', '/Customers/' . $organization->getKey() .'/'));
        }

        $parentCategoryFolder = new DataObject\Folder();
        $parentCategoryFolder->setKey(AssetResourceOrganizationFolderNames::Categories->readable());
        $parentCategoryFolder->setPath($path);
        $parentCategoryFolder->setParentId((int) $containerFolder->getId());

        try {
            $parentCategoryFolder->save();
        } catch (\Exception $exception) {
            throw new \Exception(message: $exception->getMessage() . 'CreateCategoryFolder line: 35');
        }

        return $parentCategoryFolder;
    }
}
