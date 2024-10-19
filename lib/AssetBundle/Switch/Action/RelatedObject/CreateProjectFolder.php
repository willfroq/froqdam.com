<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\RelatedObject;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;

final class CreateProjectFolder
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
            throw new \Exception(message: sprintf('CreateProjectFolder: No container folder i.e. /Customers/org-name. Path: %s does not exist!!!', '/Customers/' . $organization->getKey() . '/'));
        }

        $parentProjectFolder = new DataObject\Folder();
        $parentProjectFolder->setKey(AssetResourceOrganizationFolderNames::Projects->readable());
        $parentProjectFolder->setPath($path);
        $parentProjectFolder->setParentId((int) $containerFolder->getId());

        try {
            $parentProjectFolder->save();
        } catch (\Exception $exception) {
            throw new \Exception(message: $exception->getMessage() . 'CreateProjectFolder.php line: 37');
        }

        return $parentProjectFolder;
    }
}
