<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Organization;

final class CreateAssetFolders
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization): void
    {
        $rootAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', 'S3')
            ->addConditionParam('path = ?', '/')
            ->current();

        if (!($rootAssetFolder instanceof Asset)) {
            $rootAssetFolder = new Asset\Folder();
            $rootAssetFolder->setPath('/');
            $rootAssetFolder->setFilename('S3');
            $rootAssetFolder->setParentId(1);
            $rootAssetFolder->save();
        }

        $customerAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', 'Customer')
            ->addConditionParam('path = ?', '/S3/')
            ->current();

        if (!($customerAssetFolder instanceof Asset\Folder) && $rootAssetFolder instanceof Asset\Folder) {
            $customerAssetFolder = new Asset\Folder();
            $customerAssetFolder->setFilename('Customer');
            $customerAssetFolder->setPath('/S3/');
            $customerAssetFolder->setParentId((int) $rootAssetFolder->getId());
            $customerAssetFolder->save();
        }

        $organizationAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', $organization->getName())
            ->addConditionParam('path = ?', '/S3/Customer/')
            ->current();

        if (!($organizationAssetFolder instanceof Asset\Folder) && $customerAssetFolder instanceof Asset\Folder) {
            $organizationAssetFolder = new Asset\Folder();
            $organizationAssetFolder->setFilename((string) $organization->getName());
            $organizationAssetFolder->setPath('/S3/Customer/');
            $organizationAssetFolder->setParentId((int) $customerAssetFolder->getId());
            $organizationAssetFolder->setKey((string) $organization->getName());
            $organizationAssetFolder->save();
        }

        $assetFolderPathArray = explode('/', (string) $organization->getAssetFolder());
        $assetFolderKey = (string) end($assetFolderPathArray);

        $hashName = empty($assetFolderKey) ? substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10) : $assetFolderKey;

        $hashNameAssetFolder = (new Asset\Listing())
            ->addConditionParam('filename = ?', $hashName)
            ->addConditionParam('path = ?', '/S3/Customer/'.$organization->getName().'/')
            ->current();

        if (!($hashNameAssetFolder instanceof Asset) && $organizationAssetFolder instanceof Asset\Folder) {
            $hashNameAssetFolder = new Asset\Folder();
            $hashNameAssetFolder->setFilename($hashName);
            $hashNameAssetFolder->setPath('/S3/Customer/'.$organization->getName().'/');
            $hashNameAssetFolder->setParentId((int) $organizationAssetFolder->getId());
            $hashNameAssetFolder->save();

            $organization->setAssetFolder($hashNameAssetFolder->getPath().$hashNameAssetFolder->getKey());

            $organization->save();
        }
    }
}
