<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\RelatedObject\CreateCategoryFolder;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateCategoryFolderLevelLabel;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildCategoryFromPayload
{
    public function __construct(
        private readonly CreateCategoryFolder $createCategoryFolder,
        private readonly CreateCategoryFolderLevelLabel $createCategoryFolderLevelLabel,
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

            $categoriesName = AssetResourceOrganizationFolderNames::Categories->readable();

            $parentCategoryFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', $categoriesName)
                ->addConditionParam('o_path = ?', $rootCategoryFolder)
                ->current();

            if (!($parentCategoryFolder instanceof DataObject)) {
                $parentCategoryFolder = ($this->createCategoryFolder)($organization, $rootCategoryFolder);
            }

            $categoryFolderLevelLabel = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', $levelLabelName)
                ->addConditionParam('o_path = ?', $rootCategoryFolder . "$categoriesName/")
                ->current();

            if (!($categoryFolderLevelLabel instanceof DataObject)) {
                $categoryFolderLevelLabel = ($this->createCategoryFolderLevelLabel)($organization, $parentCategoryFolder, $levelLabelName);
            }

            $category = (new Category\Listing())
                ->addConditionParam('o_key = ?', $productCategory)
                ->addConditionParam('o_path = ?', $rootCategoryFolder . "$categoriesName/$levelLabelName/")
                ->current();

            if (!($category instanceof Category)) {
                $category = new Category();

                $category->setOrganization($organization);
                $category->setLevelLabel(ucfirst($levelLabel));
                $category->setReportingType(ucfirst($levelLabel));
                $category->setParentId((int) $categoryFolderLevelLabel->getId());
                $category->setKey($productCategory);
                $category->setPublished(true);
            }

            if (empty($category->getOrganization())) {
                $category->setOrganization($organization);
            }

            if (empty($category->getReportingType())) {
                $category->setReportingType(ucfirst($levelLabel));
            }

            if (empty($category->getLevelLabel())) {
                $category->setLevelLabel(ucfirst($levelLabel));
            }

            if (empty($category->getKey())) {
                $category->setKey($productCategory);
            }

            if ($categoryFolderLevelLabel instanceof DataObject) {
                $category->setParentId((int) $categoryFolderLevelLabel->getId());
            }

            $category->setPublished(true);

            $category->save();

            if ($category instanceof Category) {
                $categories[] = $category;
            }
        }

        return array_values(array_unique([...$categories, ...$product->getCategories()]));
    }
}
