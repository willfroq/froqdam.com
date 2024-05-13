<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildCategoryFromPayload
{
    public function __construct(
        private readonly IsPathExists $isPathExists,
    ) {
    }

    /**
     * @throws \Exception
     *
     * @param array<int, string> $actions
     *
     * @return array<int, Category>
     */
    public function __invoke(CategoryFromPayload $categoryFromPayload, Organization $organization, Product $product, SwitchUploadRequest $switchUploadRequest, array $actions): array
    {
        $categories = [];

        foreach ($categoryFromPayload->toArray() as $levelLabel => $productCategory) {
            if (empty($productCategory)) {
                continue;
            }

            $levelLabelName = ucfirst($levelLabel).'s';

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

            if (!($this->isPathExists)($productCategory, $categoriesName.'/'.$levelLabelName.'/')) {
                $category->setOrganization($organization);
                $category->setLevelLabel($levelLabelName);
                $category->setParentId((int) $categoryFolderLevelLabel->getId());
                $category->setKey($productCategory);
                $category->setPublished(true);

                $category->save();

                $categories[] = $category;
            }
        }

        return [...$categories, ...$product->getCategories()];
    }
}
