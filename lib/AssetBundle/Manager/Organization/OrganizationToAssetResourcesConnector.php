<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\Organization;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization;

class OrganizationToAssetResourcesConnector
{
    public function linkOrganizationToAssetResources(Organization $organization): void
    {
        $assetResources = [];

        foreach ($organization->getAssetResourceFolders() as $folder) {
            $data = $this->collectAssetsFromFolder($folder);
            if ($data) {
                $assetResources = array_merge($assetResources, $data);
            }
        }
        $assetResources = $assetResources ? array_unique($assetResources) : [];
        $organization->setAssetResources($assetResources);
        $organization->save();
    }

    /**
     * @return array<string|int, mixed>
     */
    private function collectAssetsFromFolder(Folder $folder): array
    {
        $assetResources = [];

        $children = $folder->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Folder) {
                $assetResources = array_merge($assetResources, $this->collectAssetsFromFolder($child));
            } elseif ($child instanceof AssetResource) {
                $assetResources[$child->getId()] = $child;
            }
        }

        return $assetResources;
    }
}
