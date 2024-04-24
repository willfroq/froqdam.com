<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization ;

final class BuildOrganizationAssetFolderIfNotExists
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $filename): void
    {
        if ($organization->getAssetFolder() === null) {
            $folder = new Folder();
            $folder->setPath('/S3/Customers/'.$organization->getName().'/');
            $folder->setKey(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10));
            $folder->save();

            $organization->setAssetFolder($folder->getPath());
            $organization->save();
        }
    }
}
