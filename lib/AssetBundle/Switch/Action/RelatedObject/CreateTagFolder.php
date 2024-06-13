<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\RelatedObject;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;

final class CreateTagFolder
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
            throw new \Exception(message: 'No container folder i.e. /Customers/org-name');
        }

        $parentTagFolder = new DataObject\Folder();
        $parentTagFolder->setKey(AssetResourceOrganizationFolderNames::Tags->readable());
        $parentTagFolder->setPath($path);
        $parentTagFolder->setParentId((int) $containerFolder->getId());

        try {
            $parentTagFolder->save();
        } catch (\Exception $exception) {
            throw new \Exception(message: $exception->getMessage());
        }

        return $parentTagFolder;
    }
}
