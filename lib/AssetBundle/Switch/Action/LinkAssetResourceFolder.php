<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization ;

final class LinkAssetResourceFolder
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization): void
    {
        if (empty($organization->getAssetResourceFolders())) {
            $assetResourceFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Assets->name)
                ->addConditionParam('o_path = ?', '/Customers/'.$organization->getName().'/')
                ->current();

            if ($assetResourceFolder instanceof Folder) {
                $organization->setAssetResourceFolders([$assetResourceFolder]);

                $organization->save();
            }
        }
    }
}
