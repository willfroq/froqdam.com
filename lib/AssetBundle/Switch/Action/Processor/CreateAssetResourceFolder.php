<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization;

final class CreateAssetResourceFolder
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $customAssetFolder): void
    {
        $rootObjectFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', 'Customers')
            ->addConditionParam('o_path = ?', '/')
            ->current();

        if (!($rootObjectFolder instanceof Folder)) {
            $rootObjectFolder = new Folder();
            $rootObjectFolder->setPath('/');
            $rootObjectFolder->setKey('Customers');
            $rootObjectFolder->setParentId(1);
            $rootObjectFolder->save();
        }

        $organizationAssetResourceFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $organization->getName())
            ->addConditionParam('o_path = ?', '/Customers/')
            ->current();

        if (!($organizationAssetResourceFolder instanceof Folder) && $rootObjectFolder instanceof Folder) {
            $organizationAssetResourceFolder = new Folder();
            $organizationAssetResourceFolder->setPath('/Customers/');
            $organizationAssetResourceFolder->setKey((string) $organization->getName());
            $organizationAssetResourceFolder->setParentId((int) $rootObjectFolder->getId());
            $organizationAssetResourceFolder->save();
        }

        $assetResourceFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $customAssetFolder)
            ->addConditionParam('o_path = ?', '/Customers/' . $organization->getName() . '/')
            ->current();

        if (!($assetResourceFolder instanceof Folder) && $organizationAssetResourceFolder instanceof Folder) {
            $assetResourceFolder = new Folder();
            $assetResourceFolder->setPath('/Customers/'.$organization->getName().'/');
            $assetResourceFolder->setKey($customAssetFolder);
            $assetResourceFolder->setParentId((int) $organizationAssetResourceFolder->getId());
            $assetResourceFolder->save();
        }

        if ($organizationAssetResourceFolder instanceof Folder) {
            $organization->setObjectFolder($organizationAssetResourceFolder->getPath().$organizationAssetResourceFolder->getKey());

            $organization->save();
        }
    }
}
