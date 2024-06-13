<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Utility\HasAssetFolder;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Organization ;

final class BuildOrganizationAssetFolderIfNotExists
{
    public function __construct(private readonly HasAssetFolder $hasAssetFolder)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $filename): void
    {
        if ($organization->getAssetFolder() === null || !($this->hasAssetFolder)($organization, $filename)) {
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
                $organizationAssetFolder->setFilename('Customer');
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
                $parentAssetFolder->setFilename((string) $organization->getName());
                $parentAssetFolder->setPath('/S3/Customer/');
                $parentAssetFolder->setParentId((int) $organizationAssetFolder->getId());
                $parentAssetFolder->setKey((string) $organization->getName());
                $parentAssetFolder->save();
            }

            if ($parentAssetFolder instanceof Asset) {
                $assetFolderPathArray = explode('/', (string) $organization->getAssetFolder());
                $assetFolderKey = (string) end($assetFolderPathArray);

                $key = empty($assetFolderKey) ? substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10) : $assetFolderKey;

                $assetFolderContainer = new Asset\Folder();
                $assetFolderContainer->setFilename($key);
                $assetFolderContainer->setPath('/S3/Customer/'.$organization->getName().'/');
                $assetFolderContainer->setParentId((int) $parentAssetFolder->getId());
                $assetFolderContainer->save();

                $organization->setAssetFolder($assetFolderContainer->getPath().$assetFolderContainer->getKey());

                $organization->save();
            }
        }
    }
}
