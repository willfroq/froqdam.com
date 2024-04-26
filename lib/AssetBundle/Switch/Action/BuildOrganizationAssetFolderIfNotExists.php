<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Organization ;

final class BuildOrganizationAssetFolderIfNotExists
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $filename): void
    {
        $rootAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', 'S3')
            ->addConditionParam('path = ?', '/')
            ->current();

        $organizationAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', 'Customer')
            ->addConditionParam('path = ?', '/S3/')
            ->current();

        if (!($organizationAssetFolder instanceof Asset\Folder) && $rootAssetFolder instanceof Asset\Folder) {
            $organizationAssetFolder = new Asset\Folder();
            $organizationAssetFolder->setKey('Customer');
            $organizationAssetFolder->setPath('/S3/');
            $organizationAssetFolder->setParentId((int) $rootAssetFolder->getId());
            $organizationAssetFolder->save();
        }

        $parentAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', $organization->getName())
            ->addConditionParam('path = ?', '/S3/Customer/')
            ->current();

        if (!($parentAssetFolder instanceof Asset\Folder) && $organizationAssetFolder instanceof Asset\Folder) {
            $parentAssetFolder = new Asset\Folder();
            $parentAssetFolder->setPath('/S3/Customer/');
            $parentAssetFolder->setParentId((int) $organizationAssetFolder->getId());
            $parentAssetFolder->setKey((string) $organization->getName());
            $parentAssetFolder->save();
        }

        $assetFolderContainer = (new Asset\Listing())
            ->addConditionParam('filename = ?', $organization->getName())
            ->addConditionParam('path = ?', '/S3/Customer/'.$organization->getName().'/')
            ->current();

        if ($organization->getAssetFolder() === null && $parentAssetFolder instanceof Asset\Folder) {
            $assetFolderContainer = new Asset\Folder();
            $assetFolderContainer->setPath('/S3/Customer/'.$organization->getName().'/');
            $assetFolderContainer->setParentId((int) $parentAssetFolder->getId());
            $assetFolderContainer->setKey(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10));
            $assetFolderContainer->save();
        }

        if ($assetFolderContainer instanceof Asset\Folder) {
            $organization->setAssetFolder($assetFolderContainer->getPath().$assetFolderContainer->getKey());

            $organization->save();
        }
    }
}
