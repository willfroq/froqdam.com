<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildCategoryFromPayload
{
    public function __construct(
        private readonly IsPathExists $isPathExists,
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws \Exception
     *
     * @param array<string, array<string, string>> $payload
     * @param array<int, string> $actions
     *
     * @return array<int, Category>
     */
    public function __invoke(array $payload, Organization $organization, Product $product, SwitchUploadRequest $switchUploadRequest, array $actions): array
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

            $categoryPath = $rootCategoryFolder . "$categoriesName/$levelLabelName/";

            if (($this->isPathExists)($switchUploadRequest, $levelLabel, $categoryPath)) {
                $message = sprintf('Related category NOT created. %s path already exists, this has to be unique.', $categoryPath);

                $actions[] = $message;

                $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                    'component' => $switchUploadRequest->eventName
                ]);
            }

            if (!($this->isPathExists)($switchUploadRequest, $levelLabel, $categoryPath)) {
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
