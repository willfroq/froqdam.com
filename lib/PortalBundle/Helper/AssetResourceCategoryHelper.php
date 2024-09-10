<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Helper;

use Pimcore\Model\DataObject\Category;

class AssetResourceCategoryHelper
{
    /**
     * @param array<int, mixed> $categories
     *
     * @return array<int|string, array<int|string, string>>
     */
    public static function getCategoryHierarchies(array $categories): array
    {
        $result = [];

        foreach ($categories as $category) {

            $label = $category->getLevelLabel();

            if (empty($label)) {
                continue;
            }

            if (!isset($result[$label])) {
                $result[$label] = [];
            }

            $result[$label][$category->getKey()] = static::getHierarchyPath($category);
        }

        return $result;
    }

    public static function getHierarchyPath(Category $category): string
    {
        $parent = static::getHighestParen($category);

        if ($parent->getId() == $category->getId()) {
            return (string) $category->getKey();
        }

        $path = StrHelper::between($category->getFullPath(), (string) $parent->getKey(), (string) $category->getKey());

        $hierarchyPath = $parent->getKey().$path.$category->getKey();

        return str_replace('/', ' > ', $hierarchyPath);
    }

    public static function getHighestParen(Category $category): Category
    {
        $parent = $category->getParent();

        if (!$parent instanceof Category) {
            return $category;
        }

        return static::getHighestParen($parent);
    }
}
