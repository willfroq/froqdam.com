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
            $rootObjectFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', 'Customers')
                ->addConditionParam('o_path = ?', '/')
                ->current();

            $organizationAssetResourceFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', $organization->getName())
                ->addConditionParam('o_path = ?', '/Customers/')
                ->current();

            if (!($organizationAssetResourceFolder instanceof DataObject) && $rootObjectFolder instanceof DataObject) {
                $organizationAssetResourceFolder = new Folder();
                $organizationAssetResourceFolder->setPath('/Customers/');
                $organizationAssetResourceFolder->setKey((string)$organization->getName());
                $organizationAssetResourceFolder->setParentId((int)$rootObjectFolder->getId());
                $organizationAssetResourceFolder->save();
            }

            $assetResourceFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Assets->name)
                ->addConditionParam('o_path = ?', '/Customers/' . $organization->getName() . '/')
                ->current();

            if (!($assetResourceFolder instanceof Folder) && $organizationAssetResourceFolder instanceof DataObject) {
                $assetResourceFolder = new Folder();
                $assetResourceFolder->setPath('/Customers/'.$organization->getName().'/');
                $assetResourceFolder->setKey(AssetResourceOrganizationFolderNames::Assets->name);
                $assetResourceFolder->setParentId((int) $organizationAssetResourceFolder->getId());
                $assetResourceFolder->save();
            }

            if ($assetResourceFolder instanceof Folder) {
                $organization->setAssetResourceFolders([$assetResourceFolder]);

                $organization->save();
            }
        }
    }
}
