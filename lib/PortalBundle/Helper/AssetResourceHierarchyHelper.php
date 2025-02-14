<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Helper;

use Pimcore\Model\DataObject\AssetResource;

class AssetResourceHierarchyHelper
{
    public static function isChild(AssetResource $assetResource): bool
    {
        return !static::isParent($assetResource);
    }

    public static function isParent(AssetResource $assetResource): bool
    {
        /** @var AssetResource $parent */
        $parent = $assetResource->getParent();

        if (empty($parent)) {
            return true;
        }

        if (!is_a($parent, AssetResource::class)) {
            return true;
        }

        return false;
    }

    public static function isParentWithoutChildren(AssetResource $assetResource): bool
    {
        return !static::isParentWithChildren($assetResource);
    }

    public static function isParentWithChildren(AssetResource $assetResource): bool
    {
        if (AssetResourceHierarchyHelper::isParent($assetResource)) {
            $children = $assetResource->getChildren([AssetResource::OBJECT_TYPE_OBJECT]);

            if (!empty($children)) {
                return true;
            }
        }

        return false;
    }

    public static function getTotalVersionCount(AssetResource $assetResource): int
    {
        $assetResource = static::getParentAssetResource($assetResource);

        $children = $assetResource->getChildren([AssetResource::OBJECT_TYPE_OBJECT]);

        return count($children);
    }

    public static function getLatestVersion(AssetResource $assetResource): AssetResource
    {
        $assetResource = static::getParentAssetResource($assetResource);

        $children = $assetResource->getChildren([AssetResource::OBJECT_TYPE_OBJECT]);

        $latest = $assetResource;

        /** @var AssetResource $child */
        foreach ($children ?? [] as $child) {
            if ($child->getAssetVersion() > $latest->getAssetVersion()) {
                $latest = $child;
            }
        }

        return $latest;
    }

    public static function getParentAssetResource(AssetResource $assetResource): AssetResource
    {
        if (static::isChild($assetResource)) {
            $assetResource = $assetResource->getParent();
        }

        return $assetResource; /** @phpstan-ignore-line */
    }

    public static function getHighestVersionNumber(AssetResource $assetResource): int
    {
        return static::getLatestVersion($assetResource)->getAssetVersion() ?: 0;
    }
}
