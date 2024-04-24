<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization ;

final class BuildOrganizationObjectFolderIfNotExists
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization): void
    {
        if ($organization->getObjectFolder() === null) {
            $folder = new Folder();
            $folder->setPath('/Customers/');
            $folder->setKey((string) $organization->getName());
            $folder->save();

            $organization->setObjectFolder($folder->getPath());
            $organization->save();
        }
    }
}
