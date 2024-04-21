<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildCategoryFromPayload
{
    /**
     * @throws \Exception
     *
     * @param array<string, array<string, string>> $payload
     * @return array<int, Category>
     */
    public function __invoke(array $payload, Organization $organization, Product $product): array
    {
        $categories = [];

        foreach ($payload['productCategories'] as $levelLabel => $productCategory) {
            if (empty($productCategory)) {
                continue;
            }

            $levelLabelName = ucfirst($levelLabel);

            $categoryNames = array_column(array: CategoryNames::cases(), column_key: 'name');

            $isValidCategoryName = in_array(needle: $levelLabelName, haystack: $categoryNames);

            if (!$isValidCategoryName) {
                continue;
            }

            $rootCategoryFolder = $organization->getObjectFolder() . '/';

            $categoriesName = AssetResourceOrganizationFolderNames::Categories->name;

            $parentCategoryFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', $categoriesName)
                ->addConditionParam('o_path = ?', $rootCategoryFolder)
                ->current();

            if (!($parentCategoryFolder instanceof DataObject)) {
                continue;
            }

            $categoryFolderLevelLabel = (new Category\Listing())
                ->addConditionParam('o_key = ?', $levelLabelName)
                ->addConditionParam('o_path = ?', $rootCategoryFolder . "$categoriesName/")
                ->current();

            if (!($categoryFolderLevelLabel instanceof Category)) {
                continue;
            }

            $category = Category::getByProducts($product)?->current(); /** @phpstan-ignore-line */
            if (!($category instanceof Category)) {
                $category = new Category();
            }

            $category->setOrganization($organization);
            $category->setLevelLabel($levelLabelName);
            $category->setParentId((int) $categoryFolderLevelLabel->getId());
            $category->setKey($productCategory);
            $category->setPublished(true);

            $category->save();

            $categories[] = $category;
        }

        return [...$categories, ...$product->getCategories()];
    }
}
